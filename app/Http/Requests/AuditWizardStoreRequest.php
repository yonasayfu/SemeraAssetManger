<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditWizardStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'site_id' => ['required', 'integer', 'exists:sites,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'asset_ids' => ['nullable', 'array'],
            'asset_ids.*' => ['integer', 'exists:assets,id'],
        ];
    }
}
