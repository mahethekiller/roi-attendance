<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage users') || $this->user()->hasRole(['super-admin', 'admin']);
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        $employeeId = $employee->id ?? $employee;
        $userId = $employee->user_id ?? null;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'employee_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_id')->ignore($employeeId),
            ],
            'card_no' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('employees', 'card_no')->ignore($employeeId),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employeeId),
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'company' => ['nullable', 'string', 'max:255'],
        ];
    }
}
