<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\InventoryRecord */
class InventoryRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product'),
            'location_code' => $this->location_code,
            'quantity_on_hand' => $this->quantity_on_hand,
            'quantity_reserved' => $this->quantity_reserved,
        ];
    }
}
