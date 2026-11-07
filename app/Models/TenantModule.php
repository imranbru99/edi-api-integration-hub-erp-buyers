<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantModule extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'module_id',
        'is_active',
        'activated_at',
        'deactivated_at',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'activated_at' => 'datetime',
            'deactivated_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
