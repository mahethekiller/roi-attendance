<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GetAttendanceApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'card_no'     => ['nullable', 'string', 'max:50'],
            'company'     => ['nullable', 'string', 'max:100'],
            'status'      => ['nullable', 'in:present,late'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:100'],
            'page'        => ['nullable', 'integer', 'min:1'],
        ];
    }
}
