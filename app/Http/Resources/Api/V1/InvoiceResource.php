<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Invoice */
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'currency' => $this->currency,
            'invoice_date' => $this->invoice_date,
            'items' => $this->whenLoaded('items'),
        ];
    }
}
