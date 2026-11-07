<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\EdiTransaction */
class EdiTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trading_partner_id' => $this->trading_partner_id,
            'standard' => $this->standard,
            'document_type' => $this->document_type,
            'direction' => $this->direction,
            'status' => $this->status,
            'control_number' => $this->control_number,
            'raw_payload' => $this->when($request->boolean('include_raw'), $this->raw_payload),
            'parsed_payload' => $this->parsed_payload,
            'canonical_payload' => $this->canonical_payload,
            'received_at' => $this->received_at,
            'processed_at' => $this->processed_at,
        ];
    }
}
