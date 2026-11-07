<?php

namespace App\Http\Requests\Api\V1;

use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

class StoreWebhookEndpointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'url' => ['required', 'url', 'max:2048'],
            'secret' => ['required', 'string', 'min:16', 'max:255'],
            'events' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('tenant_id') && TenantContext::getId()) {
            $this->merge(['tenant_id' => TenantContext::getId()]);
        }
    }
}
