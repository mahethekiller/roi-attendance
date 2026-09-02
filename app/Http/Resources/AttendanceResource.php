<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'card_no'             => $this->card_no,
            'badgenumber'         => $this->badgenumber,
            'punch_date'          => $this->punch_date ? $this->punch_date->format('Y-m-d') : null,
            'check_in_time'       => $this->check_in_time,
            'check_out_time'      => $this->check_out_time,
            'check_in_datetime'   => $this->check_in_datetime ? $this->check_in_datetime->format('Y-m-d H:i:s') : null,
            'check_out_datetime'  => $this->check_out_datetime ? $this->check_out_datetime->format('Y-m-d H:i:s') : null,
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
