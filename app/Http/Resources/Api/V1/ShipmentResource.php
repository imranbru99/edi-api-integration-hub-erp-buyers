<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Shipment */
class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shipment_number' => $this->shipment_number,
            'status' => $this->status,
            'carrier' => $this->carrier,
            'tracking_number' => $this->tracking_number,
            'shipped_at' => $this->shipped_at,
            'items' => $this->whenLoaded('items'),
        ];
    }
}
