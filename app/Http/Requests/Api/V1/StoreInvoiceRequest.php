<?php

namespace App\Http\Requests\Api\V1;

use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'invoice_number' => ['required', 'string', 'max:64'],
            'purchase_order_id' => ['nullable', 'integer', 'exists:purchase_orders,id'],
            'total_amount' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'size:3'],
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
