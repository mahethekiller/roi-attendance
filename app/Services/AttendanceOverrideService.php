<?php

namespace App\Services;

use App\Models\AttendanceOverride;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Attendance Override Service
 *
 * Implements automated attendance adjustments during sync, driven by
 * dynamic database rules configured by Super Admin, with fallback to default
 * Attendance_override_model behavior:
 * - Checks target employee ID or Card No against active AttendanceOverride rules.
 * - If check-in time is within target window (e.g. 10:00 AM - 10:20 AM),
 *   adjusts it to a random time (e.g. 09:20 AM - 09:35 AM).
 * - Adjusts check-out time to ensure at least min duration (e.g. 9 hours).
 */
class AttendanceOverrideService
{
    public const DEFAULT_TARGET_EMPLOYEE_ID = 'I2K2-0340';
    public const DEFAULT_TARGET_CARD_NO = '1234';

    protected bool $enabled;
    protected array $targetEmployeeIds;
    protected array $targetCardNos;

    public function __construct(
        ?bool $enabled = null,
        ?array $targetEmployeeIds = null,
        ?array $targetCardNos = null
    ) {
        $this->enabled = $enabled ?? (bool) config('services.biometric.override.enabled', env('ATTENDANCE_OVERRIDE_ENABLED', true));

        $configuredEmpIds = $targetEmployeeIds ?? config('services.biometric.override.target_employee_id', env('ATTENDANCE_OVERRIDE_EMPLOYEE_ID', self::DEFAULT_TARGET_EMPLOYEE_ID));
        $this->targetEmployeeIds = is_array($configuredEmpIds)
            ? $configuredEmpIds
            : array_filter(array_map('trim', explode(',', (string) $configuredEmpIds)));

        $configuredCards = $targetCardNos ?? config('services.biometric.override.target_card_no', env('ATTENDANCE_OVERRIDE_CARD_NO', self::DEFAULT_TARGET_CARD_NO));
        $this->targetCardNos = is_array($configuredCards)
            ? $configuredCards
            : array_filter(array_map('trim', explode(',', (string) $configuredCards)));
    }

    /**
     * Determine if attendance overrides are enabled.
     */
    public function isOverrideEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Find active database rule matching employee ID or card number.
     */
    public function findMatchingRule(?string $employeeId, ?string $cardNo): ?AttendanceOverride
    {
        if (!$this->isOverrideEnabled()) {
            return null;
        }

        try {
            if (Schema::hasTable('attendance_overrides')) {
                $cleanEmpId = trim((string) $employeeId);
                $cleanCard = trim((string) $cardNo);

                $rules = AttendanceOverride::active()->get();
                foreach ($rules as $rule) {
                    if ($rule->matches($cleanEmpId, $cleanCard)) {
                        return $rule;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning("AttendanceOverrideService findMatchingRule error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Check if a record matches any active override rule or fallback target.
     */
    public function matchesEmployee(?string $employeeId, ?string $cardNo): bool
    {
        if (!$this->isOverrideEnabled()) {
            return false;
        }

        // 1. Check database-defined dynamic rules first
        if ($this->findMatchingRule($employeeId, $cardNo) !== null) {
            return true;
        }

        // 2. Fallback to default/configured targets
        $cleanEmpId = trim((string) $employeeId);
        $cleanCard = trim((string) $cardNo);

        if (!empty($cleanEmpId) && in_array($cleanEmpId, $this->targetEmployeeIds, true)) {
            return true;
        }

        if (!empty($cleanCard) && in_array($cleanCard, $this->targetCardNos, true)) {
            return true;
        }

        return false;
    }

    /**
     * Adjust check-in time if within configured or rule window.
     *
     * @param string|null $checkInDatetime format: Y-m-d H:i:s
     * @param AttendanceOverride|null $rule
     * @return string|null adjusted check_in_datetime
     */
    public function adjustCheckIn(?string $checkInDatetime, ?AttendanceOverride $rule = null): ?string
    {
        if (!$this->isOverrideEnabled() || empty($checkInDatetime)) {
            return $checkInDatetime;
        }

        try {
            $dt = new DateTime($checkInDatetime);
            $timeStr = $dt->format('H:i:s');

            $windowStart = $rule?->check_in_window_start ?? '10:00:00';
            $windowEnd = $rule?->check_in_window_end ?? '10:20:00';
            $minMinute = $rule?->adjusted_in_min_minute ?? 20;
            $maxMinute = $rule?->adjusted_in_max_minute ?? 35;

            // Check if check-in time is between window start and end
            if ($timeStr > $windowStart && $timeStr < $windowEnd) {
                $randomMinutes = rand((int) $minMinute, (int) $maxMinute);
                $randomSeconds = rand(0, 59);

                $newTime = sprintf('09:%02d:%02d', $randomMinutes, $randomSeconds);
                $datePart = $dt->format('Y-m-d');

                return $datePart . ' ' . $newTime;
            }
        } catch (\Exception $e) {
            Log::warning("AttendanceOverrideService adjustCheckIn error for '{$checkInDatetime}': " . $e->getMessage());
        }

        return $checkInDatetime;
    }

    /**
     * Adjust check-out time to ensure at least min duration (e.g. 9 hours).
     *
     * @param string|null $checkInDatetime format: Y-m-d H:i:s
     * @param string|null $checkOutDatetime format: Y-m-d H:i:s
     * @param AttendanceOverride|null $rule
     * @return string|null adjusted check_out_datetime
     */
    public function adjustCheckOut(?string $checkInDatetime, ?string $checkOutDatetime, ?AttendanceOverride $rule = null): ?string
    {
        if (!$this->isOverrideEnabled() || empty($checkInDatetime) || empty($checkOutDatetime)) {
            return $checkOutDatetime;
        }

        try {
            $inDt = new DateTime($checkInDatetime);
            $outDt = new DateTime($checkOutDatetime);

            // If same time (e.g. checked in but not actually checked out yet)
            if ($inDt == $outDt) {
                return $checkOutDatetime;
            }

            $interval = $outDt->diff($inDt);
            $hours = $interval->h + ($interval->days * 24);
            $minutes = $interval->i;
            $totalMinutes = ($hours * 60) + $minutes;

            $durationHours = $rule?->min_duration_hours ?? 9.00;
            $targetMinutes = (int) round($durationHours * 60);

            if ($totalMinutes < $targetMinutes) {
                // Introduce realistic random variation between target (e.g. 9 hrs) and target + 30 mins (9h - 9h 30m)
                $extraMinutes = rand(1, 30);
                $extraSeconds = rand(0, 59);

                $newOutDt = clone $inDt;
                $newOutDt->modify("+{$targetMinutes} minutes +{$extraMinutes} minutes +{$extraSeconds} seconds");
                return $newOutDt->format('Y-m-d H:i:s');
            }
        } catch (\Exception $e) {
            Log::warning("AttendanceOverrideService adjustCheckOut error: " . $e->getMessage());
        }

        return $checkOutDatetime;
    }

    /**
     * Process and adjust an array/collection of attendance objects or arrays.
     *
     * @param mixed $data
     * @return mixed
     */
    public function adjustAttendanceList(mixed $data): mixed
    {
        if (!$this->isOverrideEnabled() || empty($data)) {
            return $data;
        }

        foreach ($data as &$row) {
            $empId = is_object($row) ? ($row->employee_id ?? $row->badgenumber ?? null) : ($row['employee_id'] ?? $row['badgenumber'] ?? null);
            $cardNo = is_object($row) ? ($row->card_no ?? null) : ($row['card_no'] ?? null);

            $rule = $this->findMatchingRule($empId, $cardNo);

            if ($rule || $this->matchesEmployee($empId, $cardNo)) {
                $checkIn = is_object($row) ? ($row->check_in_datetime ?? null) : ($row['check_in_datetime'] ?? null);
                if (!empty($checkIn)) {
                    $adjustedIn = $this->adjustCheckIn((string) $checkIn, $rule);
                    if ($adjustedIn !== $checkIn) {
                        if (is_object($row)) {
                            $row->check_in_datetime = $adjustedIn;
                            $row->check_in_time = date('H:i:s', strtotime($adjustedIn));
                        } else {
                            $row['check_in_datetime'] = $adjustedIn;
                            $row['check_in_time'] = date('H:i:s', strtotime($adjustedIn));
                        }
                    }

                    $checkOut = is_object($row) ? ($row->check_out_datetime ?? null) : ($row['check_out_datetime'] ?? null);
                    if (!empty($checkOut) && $checkIn !== $checkOut) {
                        $effectiveIn = is_object($row) ? $row->check_in_datetime : $row['check_in_datetime'];
                        $adjustedOut = $this->adjustCheckOut((string) $effectiveIn, (string) $checkOut, $rule);
                        if ($adjustedOut !== $checkOut) {
                            if (is_object($row)) {
                                $row->check_out_datetime = $adjustedOut;
                                $row->check_out_time = date('H:i:s', strtotime($adjustedOut));
                            } else {
                                $row['check_out_datetime'] = $adjustedOut;
                                $row['check_out_time'] = date('H:i:s', strtotime($adjustedOut));
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }
}
