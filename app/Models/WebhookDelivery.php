<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebhookDelivery extends Model
{
    protected $table = 'webhook_deliveries';

    protected $fillable = [
        'webhook_endpoint_id',
        'event',
        'payload',
        'status',
        'attempts',
        'response_code',
        'next_retry_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'next_retry_at' => 'datetime',
        ];
    }

    public function webhookEndpoint(): BelongsTo
    {
        return $this->belongsTo(WebhookEndpoint::class);
    }
}
