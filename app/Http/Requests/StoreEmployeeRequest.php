<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage users') || $this->user()->hasRole(['super-admin', 'admin']);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'employee_id' => ['required', 'string', 'max:50', 'unique:employees,employee_id'],
            'card_no' => ['nullable', 'string', 'max:50', 'unique:employees,card_no'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:employees,email'],
            'company' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
