<?php

namespace App\Http\Requests;

use App\Models\Staff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('staff.manage') ?? false;
    }

    public function rules(): array
    {
        $staffId = $this->route('staff')?->id ?? null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('staff', 'email')->ignore($staffId),
            ],
            'password' => ['nullable', 'string', 'min:8', 'max:255', 'confirmed'],
            'account_status' => ['required', Rule::in([Staff::STATUS_PENDING, Staff::STATUS_ACTIVE, Staff::STATUS_SUSPENDED])],
            'account_type' => ['required', Rule::in([Staff::TYPE_INTERNAL, Staff::TYPE_EXTERNAL])],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
            'staff_id' => ['nullable', Rule::exists('staff', 'id')],
        ];
    }
}
