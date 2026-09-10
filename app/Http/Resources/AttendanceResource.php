<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $punchDate = $this->punch_date instanceof \DateTimeInterface
            ? $this->punch_date->format('Y-m-d')
            : ($this->punch_date ? Carbon::parse($this->punch_date)->format('Y-m-d') : null);

        // Format check_in_datetime
        $checkInDatetime = null;
        if (!empty($this->check_in_datetime)) {
            $checkInDatetime = $this->check_in_datetime instanceof \DateTimeInterface
                ? $this->check_in_datetime->format('Y-m-d H:i:s')
                : Carbon::parse($this->check_in_datetime)->format('Y-m-d H:i:s');
        } elseif ($punchDate && !empty($this->check_in_time)) {
            $checkInDatetime = "{$punchDate} {$this->check_in_time}";
        } elseif ($punchDate) {
            $checkInDatetime = "{$punchDate} 00:00:00";
        }

        // Format check_out_datetime
        $checkOutDatetime = null;
        if (!empty($this->check_out_datetime)) {
            $formattedOut = $this->check_out_datetime instanceof \DateTimeInterface
                ? $this->check_out_datetime->format('Y-m-d H:i:s')
                : Carbon::parse($this->check_out_datetime)->format('Y-m-d H:i:s');

            if ($formattedOut === $checkInDatetime) {
                $checkOutDatetime = $punchDate ? "{$punchDate} 00:00:00" : null;
            } else {
                $checkOutDatetime = $formattedOut;
            }
        } elseif ($punchDate && !empty($this->check_out_time) && $this->check_out_time !== '00:00:00' && $this->check_out_time !== $this->check_in_time) {
            $checkOutDatetime = "{$punchDate} {$this->check_out_time}";
        } elseif ($punchDate) {
            $checkOutDatetime = "{$punchDate} 00:00:00";
        }

        return [
            'id'                  => $this->id,
            'card_no'             => $this->card_no,
            'badgenumber'         => $this->badgenumber,
            'punch_date'          => $punchDate,
            'check_in_time'       => $this->check_in_time,
            'check_out_time'      => $this->check_out_time,
            'check_in_datetime'   => $checkInDatetime,
            'check_out_datetime'  => $checkOutDatetime,
            'total_time'          => $this->total_time,
            'show_status'         => $this->show_status,
            'employee'            => $this->whenLoaded('employee', function () {
                return $this->employee ? [
                    'employee_id' => $this->employee->employee_id,
                    'full_name'   => $this->employee->full_name,
                    'first_name'  => $this->employee->first_name,
                    'last_name'   => $this->employee->last_name,
                    'email'       => $this->employee->email,
                    'company'     => $this->employee->company,
                ] : null;
            }, function () {
                return $this->employee ? [
                    'employee_id' => $this->employee->employee_id,
                    'full_name'   => $this->employee->full_name,
                    'email'       => $this->employee->email,
                    'company'     => $this->employee->company,
                ] : null;
            }),
            'created_at'          => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
