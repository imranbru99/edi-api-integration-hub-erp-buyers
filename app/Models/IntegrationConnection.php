<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegrationConnection extends Model
{
    use BelongsToTenant;

    protected $table = 'integration_connections';

    protected $fillable = [
        'tenant_id',
        'name',
        'connector_type',
        'status',
        'config_encrypted',
        'last_sync_at',
        'health_status',
    ];

    protected $hidden = [
        'config_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'last_sync_at' => 'datetime',
        ];
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(IntegrationSyncLog::class);
    }
}
