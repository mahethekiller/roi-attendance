<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class HRsaleAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'punch_date'  => ['nullable', 'date_format:Y-m-d'],
            'start_date'  => ['nullable', 'date_format:Y-m-d'],
            'end_date'    => ['nullable', 'date_format:Y-m-d'],
            'company_id'  => ['nullable'],
            'employee_id' => ['nullable'],
            'card_no'     => ['nullable', 'string', 'max:50'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error' => 'Validation Error',
            'messages' => $validator->errors()
        ], 422));
    }
}
