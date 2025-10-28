<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditScanUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'found' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
