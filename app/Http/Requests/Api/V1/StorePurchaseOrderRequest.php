<?php

namespace App\Http\Requests\Api\V1;

use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'trading_partner_id' => ['nullable', 'integer', 'exists:trading_partners,id'],
            'po_number' => ['required', 'string', 'max:64'],
            'order_date' => ['nullable', 'date'],
            'total_amount' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'size:3'],
            'items' => ['nullable', 'array'],
            'items.*.line_number' => ['nullable', 'string'],
            'items.*.sku' => ['nullable', 'string'],
            'items.*.quantity' => ['nullable', 'numeric'],
            'items.*.unit_price' => ['nullable', 'numeric'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('tenant_id') && TenantContext::getId()) {
            $this->merge(['tenant_id' => TenantContext::getId()]);
        }
    }
}
