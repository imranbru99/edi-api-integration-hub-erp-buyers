<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TradingPartner */
class TradingPartnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'partner_type' => $this->partner_type,
            'status' => $this->status,
            'onboarding_step' => $this->onboarding_step,
            'default_standard' => $this->default_standard,
            'country' => $this->country,
            'currency' => $this->currency,
            'created_at' => $this->created_at,
        ];
    }
}
