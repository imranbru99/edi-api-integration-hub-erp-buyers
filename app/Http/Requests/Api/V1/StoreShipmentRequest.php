<?php

namespace App\Http\Requests\Api\V1;

use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'purchase_order_id' => ['nullable', 'integer', 'exists:purchase_orders,id'],
            'shipment_number' => ['required', 'string', 'max:64'],
            'carrier' => ['nullable', 'string', 'max:120'],
            'tracking_number' => ['nullable', 'string', 'max:120'],
            'items' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('tenant_id') && TenantContext::getId()) {
            $this->merge(['tenant_id' => TenantContext::getId()]);
        }
    }
}
