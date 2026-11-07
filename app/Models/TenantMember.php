<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantMember extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'role_code',
        'is_owner',
        'status',
        'invited_at',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'is_owner' => 'boolean',
            'invited_at' => 'datetime',
            'joined_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
