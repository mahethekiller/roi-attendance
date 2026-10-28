<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && (
            $this->user()->can('companies.edit') ||
            $this->user()->hasRole(['super-admin', 'admin', 'hr'])
        );
    }

    public function rules(): array
    {
        $company = $this->route('company');
        $companyId = $company instanceof \App\Models\Company ? $company->id : $company;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name')->ignore($companyId),
            ],
            'code' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
