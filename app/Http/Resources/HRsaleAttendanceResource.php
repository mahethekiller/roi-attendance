<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HRsaleAttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array matching HRsale API schema.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Format punch date
        $punchDate = $this->punch_date instanceof \DateTimeInterface
            ? $this->punch_date->format('Y-m-d')
            : ($this->punch_date ? Carbon::parse($this->punch_date)->format('Y-m-d') : date('Y-m-d'));

        // Format clock_in
        $clockIn = '';
        if (!empty($this->check_in_time)) {
            $clockIn = strlen($this->check_in_time) === 8 ? $this->check_in_time : Carbon::parse($this->check_in_time)->format('H:i:s');
        } elseif ($this->check_in_datetime) {
            $clockIn = Carbon::parse($this->check_in_datetime)->format('H:i:s');
        }

        // Format check_in_datetime
        $checkInDatetime = null;
        if (!empty($this->check_in_datetime)) {
            $checkInDatetime = $this->check_in_datetime instanceof \DateTimeInterface
                ? $this->check_in_datetime->format('Y-m-d H:i:s')
                : Carbon::parse($this->check_in_datetime)->format('Y-m-d H:i:s');
        } elseif (!empty($clockIn)) {
            $checkInDatetime = "{$punchDate} {$clockIn}";
        } else {
            $checkInDatetime = "{$punchDate} 00:00:00";
        }

        // Format check_out_datetime (null at first until employee punches out at last)
        $checkOutDatetime = null;
        if (!empty($this->check_out_datetime)) {
            $formattedOut = $this->check_out_datetime instanceof \DateTimeInterface
                ? $this->check_out_datetime->format('Y-m-d H:i:s')
                : Carbon::parse($this->check_out_datetime)->format('Y-m-d H:i:s');

            if ($formattedOut !== $checkInDatetime) {
                $checkOutDatetime = $formattedOut;
            }
        } elseif (!empty($this->check_out_time) && $this->check_out_time !== '00:00:00' && $this->check_out_time !== $this->check_in_time) {
            $checkOutDatetime = "{$punchDate} {$this->check_out_time}";
        }

        // Format clock_out (empty until checked out)
        $clockOut = '';
        if ($checkOutDatetime !== null) {
            if (!empty($this->check_out_time) && $this->check_out_time !== $this->check_in_time) {
                $clockOut = strlen($this->check_out_time) === 8 ? $this->check_out_time : Carbon::parse($this->check_out_time)->format('H:i:s');
            } else {
                $clockOut = Carbon::parse($checkOutDatetime)->format('H:i:s');
            }
        }

        // Calculate total work duration
        $totalWork = '00:00:00';
        if (!empty($clockIn) && !empty($clockOut)) {
            try {
                $start = Carbon::parse("{$punchDate} {$clockIn}");
                $end = Carbon::parse("{$punchDate} {$clockOut}");
                if ($end->greaterThanOrEqualTo($start)) {
                    $diffInSeconds = abs((int) $end->diffInSeconds($start));
                    $hours = intdiv($diffInSeconds, 3600);
                    $minutes = intdiv($diffInSeconds % 3600, 60);
                    $seconds = $diffInSeconds % 60;
                    $totalWork = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                }
            } catch (\Throwable $e) {
                $totalWork = '00:00:00';
            }
        }

        $employeeName = $this->employee?->full_name;
        if (empty($employeeName) || trim($employeeName) === '') {
            $employeeName = 'Employee #' . ($this->badgenumber ?? $this->card_no);
        }

        $companyName = $this->employee?->company ?: 'ROI Attendance';
        $employeeId = (string) ($this->employee?->employee_id ?: ($this->badgenumber ?: $this->card_no));

        return [
            'time_attendance_id'  => (string) $this->id,
            'card_no'             => (string) $this->card_no,
            'punch_date'          => $punchDate,
            'clock_in'            => $clockIn,
            'clock_out'           => $clockOut,
            'check_in_datetime'   => $checkInDatetime,
            'check_out_datetime'  => $checkOutDatetime,
            'total_work'          => $totalWork,
            'employee_id'         => $employeeId,
            'employee_name'       => $employeeName,
            'company_name'        => $companyName,
        ];
    }
}
