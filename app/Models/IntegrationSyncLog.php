<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegrationSyncLog extends Model
{
    protected $table = 'integration_sync_logs';

    protected $fillable = [
        'integration_connection_id',
        'direction',
        'entity_type',
        'status',
        'records_processed',
        'records_failed',
        'message',
        'meta',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function integrationConnection(): BelongsTo
    {
        return $this->belongsTo(IntegrationConnection::class);
    }
}
