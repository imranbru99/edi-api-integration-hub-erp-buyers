<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\PurchaseOrder */
class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'po_number' => $this->po_number,
            'status' => $this->status,
            'order_date' => $this->order_date,
            'total_amount' => $this->total_amount,
            'currency' => $this->currency,
            'items' => $this->whenLoaded('items'),
        ];
    }
}
