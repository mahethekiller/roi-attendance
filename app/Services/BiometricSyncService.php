<?php

namespace App\Services;

use App\Models\Attendance;
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
            $skippedUnregisteredCount = 0;

            // Pre-fetch all registered card numbers and employee IDs for fast O(1) hash lookup
            $registeredCards = \App\Models\Employee::whereNotNull('card_no')
                ->where('card_no', '!=', '')
                ->pluck('card_no')
                ->map(fn($v) => trim((string)$v))
                ->flip();

            $registeredBadgeNumbers = \App\Models\Employee::whereNotNull('employee_id')
                ->where('employee_id', '!=', '')
                ->pluck('employee_id')
                ->map(fn($v) => trim((string)$v))
                ->flip();

            foreach ($punchRecords as $obj) {
                $cardNo = is_array($obj) ? ($obj['card_no'] ?? null) : ($obj->card_no ?? null);
                $badgeNumber = is_array($obj) ? ($obj['badgenumber'] ?? null) : ($obj->badgenumber ?? null);
                $punchDateRaw = is_array($obj) ? ($obj['punch_date'] ?? null) : ($obj->punch_date ?? null);
                $minCheckTime = is_array($obj) ? ($obj['minchecktime'] ?? null) : ($obj->minchecktime ?? null);
                $maxCheckTime = is_array($obj) ? ($obj['maxchecktime'] ?? null) : ($obj->maxchecktime ?? null);
                $minTime = is_array($obj) ? ($obj['mintime'] ?? null) : ($obj->mintime ?? null);
                $maxTime = is_array($obj) ? ($obj['maxtime'] ?? null) : ($obj->maxtime ?? null);

                if (empty($cardNo) || empty($punchDateRaw)) {
                    continue;
                }

                $cleanCard = trim((string)$cardNo);
                $cleanBadge = !empty($badgeNumber) ? trim((string)$badgeNumber) : '';

                // Verify employee exists in local database by card_no or employee_id / badgenumber
                $isRegistered = $registeredCards->has($cleanCard) || (!empty($cleanBadge) && $registeredBadgeNumbers->has($cleanBadge));

                if (!$isRegistered) {
                    $skippedUnregisteredCount++;
                    continue;
                }

                $punchDate = Carbon::parse($punchDateRaw)->format('Y-m-d');

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
                    // If the record was already synced and assigned an adjusted check-in, lock and preserve it
                    // so it does NOT jump around to a new random minute/second on every sync cycle!
                    if ($existing && !empty($existing->check_in_datetime)) {
                        $minCheckTime = $existing->check_in_datetime instanceof \DateTimeInterface
                            ? $existing->check_in_datetime->format('Y-m-d H:i:s')
                            : (string) $existing->check_in_datetime;
                        $minTime = $existing->check_in_time;
                    } elseif (!empty($minCheckTime)) {
                        $adjustedIn = $this->overrideService->adjustCheckIn($minCheckTime, $overrideRule);
                        if ($adjustedIn !== $minCheckTime) {
                            $minCheckTime = $adjustedIn;
                            $minTime = date('H:i:s', strtotime($adjustedIn));
                        }
                    }

                    if (!empty($maxCheckTime)) {
                        if ($rawMinCheckTime === $rawMaxCheckTime) {
                            // Only one punch recorded so far: employee has not checked out yet
                            $maxCheckTime = $minCheckTime;
                            $maxTime = $minTime;
                        } else {
                            $adjustedOut = $this->overrideService->adjustCheckOut($minCheckTime, $maxCheckTime, $overrideRule);
                            if ($adjustedOut !== $maxCheckTime) {
                                $maxCheckTime = $adjustedOut;
                                $maxTime = date('H:i:s', strtotime($adjustedOut));
                            }
                        }
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

            $successMsg = "Sync complete: {$importedCount} new entries inserted, {$updatedCount} updated, {$skippedUnregisteredCount} skipped (unregistered employees).";

            // Save to sync_logs DB table
            \App\Models\SyncLog::create([
                'trigger_type'   => $triggerType,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'status'         => 'success',
                'imported_count' => $importedCount,
                'updated_count'  => $updatedCount,
                'message'        => $successMsg,
                'payload_summary' => [
                    'total_records_received' => count($punchRecords),
                    'skipped_unregistered'   => $skippedUnregisteredCount,
                ],
            ]);

            Log::info("Biometric Sync Completed: {$importedCount} inserted, {$updatedCount} updated, {$skippedUnregisteredCount} skipped for range {$startDate} to {$endDate}.");

            return [
                'success'              => true,
                'message'              => $successMsg,
                'imported'             => $importedCount,
                'updated'              => $updatedCount,
                'skipped_unregistered' => $skippedUnregisteredCount,
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
