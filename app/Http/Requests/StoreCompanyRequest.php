<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && (
            $this->user()->can('companies.create') ||
            $this->user()->hasRole(['super-admin', 'admin', 'hr'])
        );
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'code' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
