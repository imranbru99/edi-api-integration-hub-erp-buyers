<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\MappingProfile */
class MappingProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'standard' => $this->standard,
            'document_type' => $this->document_type,
            'direction' => $this->direction,
            'status' => $this->status,
            'versions' => $this->whenLoaded('versions'),
        ];
    }
}
