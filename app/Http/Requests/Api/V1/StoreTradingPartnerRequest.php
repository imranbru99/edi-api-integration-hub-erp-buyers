<?php

namespace App\Http\Requests\Api\V1;

use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

class StoreTradingPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:64'],
            'partner_type' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'size:2'],
            'currency' => ['nullable', 'string', 'size:3'],
            'default_standard' => ['nullable', 'string', 'max:32'],
            'settings' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('tenant_id') && TenantContext::getId()) {
            $this->merge(['tenant_id' => TenantContext::getId()]);
        }
    }
}
