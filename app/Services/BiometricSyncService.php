<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\OtherCompanyAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiometricSyncService
{
    protected string $apiUrl;
    protected AttendanceOverrideService $overrideService;

    public function __construct(?AttendanceOverrideService $overrideService = null)
    {
        $this->apiUrl = config('services.biometric.url', env('BIOMETRIC_API_URL', 'http://103.25.129.247/prac1111/practice/practice2/get_today_data_api_new.php'));
        $this->overrideService = $overrideService ?? app(AttendanceOverrideService::class);
    }

    public function sync(?string $startDate = null, ?string $endDate = null, string $triggerType = 'cron'): array
    {
        // Default: sync last 3 days up to today
        $startDate = $startDate ?: Carbon::now('Asia/Kolkata')->subDays(3)->format('Y-m-d');
        $endDate = $endDate ?: Carbon::now('Asia/Kolkata')->format('Y-m-d');

        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post($this->apiUrl, [
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                ]);

            if (!$response->successful()) {
                $errorMsg = "Biometric API responded with HTTP status {$response->status()}";
                Log::error("Biometric Sync API HTTP error: {$response->status()} - {$response->body()}");

                \App\Models\SyncLog::create([
                    'trigger_type'   => $triggerType,
                    'start_date'     => $startDate,
                    'end_date'       => $endDate,
                    'status'         => 'failed',
                    'imported_count' => 0,
                    'updated_count'  => 0,
                    'message'        => $errorMsg,
                    'payload_summary' => ['http_status' => $response->status()],
                ]);

                return [
                    'success'  => false,
                    'message'  => $errorMsg,
                    'imported' => 0,
                    'updated'  => 0,
                ];
            }

            $result = $response->json();

            if (!isset($result['status']) || $result['status'] != 1) {
                $errorMsg = $result['message'] ?? 'API returned invalid status';
                Log::warning("Biometric Sync API returned unsuccessful status: {$errorMsg}");

                \App\Models\SyncLog::create([
                    'trigger_type'   => $triggerType,
                    'start_date'     => $startDate,
                    'end_date'       => $endDate,
                    'status'         => 'failed',
                    'imported_count' => 0,
                    'updated_count'  => 0,
                    'message'        => $errorMsg,
                    'payload_summary' => $result,
                ]);

                return [
                    'success'  => false,
                    'message'  => $errorMsg,
                    'imported' => 0,
                    'updated'  => 0,
                ];
            }

            $punchRecords = $result['data'] ?? [];
            $importedCount = 0;
            $updatedCount = 0;
            $otherImportedCount = 0;
            $otherUpdatedCount = 0;
            $skippedCount = 0;

            // Pre-fetch all registered employees by card_no and employee_id for fast O(1) lookup
            $employeesByCard = \App\Models\Employee::whereNotNull('card_no')
                ->where('card_no', '!=', '')
                ->get()
                ->keyBy(fn($v) => trim((string)$v->card_no));

            $employeesByBadge = \App\Models\Employee::whereNotNull('employee_id')
                ->where('employee_id', '!=', '')
                ->get()
                ->keyBy(fn($v) => trim((string)$v->employee_id));

            foreach ($punchRecords as $obj) {
                $cardNo = is_array($obj) ? ($obj['card_no'] ?? null) : ($obj->card_no ?? null);
                $badgeNumber = is_array($obj) ? ($obj['badgenumber'] ?? null) : ($obj->badgenumber ?? null);
                $punchDateRaw = is_array($obj) ? ($obj['punch_date'] ?? null) : ($obj->punch_date ?? null);
                $minCheckTime = is_array($obj) ? ($obj['minchecktime'] ?? null) : ($obj->minchecktime ?? null);
                $maxCheckTime = is_array($obj) ? ($obj['maxchecktime'] ?? null) : ($obj->maxchecktime ?? null);
                $minTime = is_array($obj) ? ($obj['mintime'] ?? null) : ($obj->mintime ?? null);
                $maxTime = is_array($obj) ? ($obj['maxtime'] ?? null) : ($obj->maxtime ?? null);

                if (empty($cardNo) || empty($punchDateRaw)) {
                    $skippedCount++;
                    continue;
                }

                $cleanCard = trim((string)$cardNo);
                $cleanBadge = !empty($badgeNumber) ? trim((string)$badgeNumber) : '';

                $emp = $employeesByCard->get($cleanCard) ?? (!empty($cleanBadge) ? $employeesByBadge->get($cleanBadge) : null);
                $punchDate = Carbon::parse($punchDateRaw)->format('Y-m-d');

                // Determine whether this record belongs to ROI Mantra
                $isRoiMantra = $emp && (empty($emp->company) || stripos($emp->company, 'ROI Mantra') !== false);

                if (!$isRoiMantra) {
                    // Record in other_company_attendances table
                    $otherCompanyName = ($emp && !empty($emp->company))
                        ? $emp->company
                        : (is_array($obj) ? ($obj['company'] ?? 'External Company') : ($obj->company ?? 'External Company'));

                    $otherEmpName = $emp
                        ? $emp->full_name
                        : (is_array($obj) ? ($obj['name'] ?? null) : ($obj->name ?? null));

                    $existingOther = OtherCompanyAttendance::where('card_no', $cardNo)
                        ->whereDate('punch_date', $punchDate)
                        ->first();

                    $rawMin = $minCheckTime;
                    $rawMax = $maxCheckTime;
                    $actualMaxCheckTime = ($rawMin === $rawMax) ? null : $rawMax;
                    $actualMaxTime = ($rawMin === $rawMax) ? null : $maxTime;
                    $status = 'present';

                    if ($existingOther) {
                        $hasChanged = ($existingOther->check_out_time != $actualMaxTime 
                            || $existingOther->check_out_datetime != $actualMaxCheckTime
                            || $existingOther->check_in_time != $minTime
                            || $existingOther->check_in_datetime != $minCheckTime
                            || $existingOther->company_name != $otherCompanyName);

                        if ($hasChanged) {
                            $existingOther->update([
                                'badgenumber'        => $badgeNumber ?: $existingOther->badgenumber,
                                'employee_name'      => $otherEmpName ?? $existingOther->employee_name,
                                'company_name'       => $otherCompanyName,
                                'check_in_datetime'  => $minCheckTime,
                                'check_in_time'      => $minTime,
                                'check_out_datetime' => $actualMaxCheckTime,
                                'check_out_time'     => $actualMaxTime,
                                'show_status'        => $status,
                                'raw_payload'        => is_array($obj) ? $obj : (array)$obj,
                            ]);
                            $otherUpdatedCount++;
                        }
                    } else {
                        OtherCompanyAttendance::create([
                            'card_no'            => $cardNo,
                            'badgenumber'        => $badgeNumber,
                            'employee_name'      => $otherEmpName,
                            'company_name'       => $otherCompanyName,
                            'punch_date'         => $punchDate,
                            'check_in_datetime'  => $minCheckTime,
                            'check_out_datetime' => $actualMaxCheckTime,
                            'check_in_time'      => $minTime,
                            'check_out_time'     => $actualMaxTime,
                            'show_status'        => $status,
                            'raw_payload'        => is_array($obj) ? $obj : (array)$obj,
                        ]);
                        $otherImportedCount++;
                    }

                    continue;
                }

                $existing = Attendance::where('card_no', $cardNo)
                    ->whereDate('punch_date', $punchDate)
                    ->first();

                // Keep track of original raw punch times before any override transformation
                $rawMinCheckTime = $minCheckTime;
                $rawMaxCheckTime = $maxCheckTime;

                // Apply automated attendance override for target employee/card
                $isTargetOverride = $this->overrideService->matchesEmployee($badgeNumber, $cardNo);
                $overrideRule = $isTargetOverride ? $this->overrideService->findMatchingRule($badgeNumber, $cardNo) : null;

                if ($isTargetOverride) {
                    // Check-in stays as is (keeps original biometric punch time)
                    $minCheckTime = $rawMinCheckTime;
                    $minTime = !empty($rawMinCheckTime) ? date('H:i:s', strtotime($rawMinCheckTime)) : $minTime;

                    if (!empty($maxCheckTime)) {
                        if ($rawMinCheckTime === $rawMaxCheckTime) {
                            if ($punchDate < Carbon::today('Asia/Kolkata')->format('Y-m-d')) {
                                // For past dates where only one punch was recorded, apply the adjusted plan check-out
                                $adjustedOut = $this->overrideService->calculateAdjustedPlanCheckOut($minCheckTime, $overrideRule);
                                $maxCheckTime = $adjustedOut;
                                $maxTime = date('H:i:s', strtotime($adjustedOut));
                            } else {
                                // Only one punch recorded today so far: employee has not checked out yet
                                $maxCheckTime = null;
                                $maxTime = null;
                            }
                        } else {
                            // If the existing record already has an adjusted check-out that satisfies the minimum shift requirement (e.g. >= 9 hours),
                            // preserve it so repeated sync cycles do not re-randomize checkout between 9 - 9:30 hrs!
                            $hasExistingAdjustedOut = false;
                            if ($existing && !empty($existing->check_out_datetime) && !empty($existing->check_in_datetime)) {
                                $existingInTs = $existing->check_in_datetime instanceof \DateTimeInterface
                                    ? $existing->check_in_datetime->getTimestamp()
                                    : strtotime((string) $existing->check_in_datetime);
                                $existingOutTs = $existing->check_out_datetime instanceof \DateTimeInterface
                                    ? $existing->check_out_datetime->getTimestamp()
                                    : strtotime((string) $existing->check_out_datetime);

                                $minHours = $overrideRule?->min_duration_hours ?? 9.00;
                                if ($existingOutTs > $existingInTs && ($existingOutTs - $existingInTs) >= ($minHours * 3600)) {
                                    $hasExistingAdjustedOut = true;
                                    $maxCheckTime = $existing->check_out_datetime instanceof \DateTimeInterface
                                        ? $existing->check_out_datetime->format('Y-m-d H:i:s')
                                        : (string) $existing->check_out_datetime;
                                    $maxTime = $existing->check_out_time;
                                }
                            }

                            if (!$hasExistingAdjustedOut) {
                                $adjustedOut = $this->overrideService->adjustCheckOut($minCheckTime, $maxCheckTime, $overrideRule);
                                if ($adjustedOut !== $maxCheckTime) {
                                    $maxCheckTime = $adjustedOut;
                                    $maxTime = date('H:i:s', strtotime($adjustedOut));
                                }
                            }
                        }
                    }
                } else {
                    if ($rawMinCheckTime === $rawMaxCheckTime) {
                        // Only one punch recorded so far: employee has not checked out yet
                        $maxCheckTime = null;
                        $maxTime = null;
                    }
                }

                // Status is strictly 'present' for any recorded punch (no 'late')
                $status = 'present';

                if ($existing) {
                    $hasCheckOutChanged = ($existing->check_out_time != $maxTime || $existing->check_out_datetime != $maxCheckTime);
                    $hasCheckInChanged = (!$isTargetOverride && ($existing->check_in_time != $minTime || $existing->check_in_datetime != $minCheckTime));
                    $hasStatusChanged = ($existing->show_status != $status);

                    if ($hasCheckOutChanged || $hasCheckInChanged || $hasStatusChanged) {
                        $existing->update([
                            'check_in_datetime'  => $minCheckTime,
                            'check_in_time'      => $minTime,
                            'check_out_datetime' => $maxCheckTime,
                            'check_out_time'     => $maxTime,
                            'show_status'        => $status,
                        ]);
                        $updatedCount++;
                    }
                } else {
                    Attendance::create([
                        'card_no'            => $cardNo,
                        'badgenumber'        => $badgeNumber,
                        'punch_date'         => $punchDate,
                        'check_in_datetime'  => $minCheckTime,
                        'check_out_datetime' => $maxCheckTime,
                        'check_in_time'      => $minTime,
                        'check_out_time'     => $maxTime,
                        'show_status'        => $status,
                    ]);
                    $importedCount++;
                }
            }

            $successMsg = "Sync complete: {$importedCount} ROI Mantra inserted, {$updatedCount} updated; {$otherImportedCount} Other Company inserted, {$otherUpdatedCount} updated.";

            // Save to sync_logs DB table
            \App\Models\SyncLog::create([
                'trigger_type'   => $triggerType,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'status'         => 'success',
                'imported_count' => $importedCount + $otherImportedCount,
                'updated_count'  => $updatedCount + $otherUpdatedCount,
                'message'        => $successMsg,
                'payload_summary' => [
                    'total_records_received' => count($punchRecords),
                    'roi_mantra_imported'    => $importedCount,
                    'roi_mantra_updated'     => $updatedCount,
                    'other_company_imported' => $otherImportedCount,
                    'other_company_updated'  => $otherUpdatedCount,
                    'skipped_invalid'        => $skippedCount,
                ],
            ]);

            Log::info("Biometric Sync Completed: {$importedCount}/{$updatedCount} (ROI) | {$otherImportedCount}/{$otherUpdatedCount} (Other) for range {$startDate} to {$endDate}.");

            return [
                'success'                => true,
                'message'                => $successMsg,
                'imported'               => $importedCount,
                'updated'                => $updatedCount,
                'other_company_imported' => $otherImportedCount,
                'other_company_updated'  => $otherUpdatedCount,
            ];

        } catch (\Exception $e) {
            Log::error("Biometric Sync Exception: " . $e->getMessage());

            \App\Models\SyncLog::create([
                'trigger_type'   => $triggerType,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'status'         => 'failed',
                'imported_count' => 0,
                'updated_count'  => 0,
                'message'        => $e->getMessage(),
                'payload_summary' => ['exception' => $e->getMessage()],
            ]);

            return [
                'success' => false,
                'message' => "Biometric Sync Exception: " . $e->getMessage(),
                'imported' => 0,
                'updated'  => 0,
            ];
        }
    }
}
