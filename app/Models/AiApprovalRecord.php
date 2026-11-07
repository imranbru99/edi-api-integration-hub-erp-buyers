<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiApprovalRecord extends Model
{
    use BelongsToTenant;

    protected $table = 'ai_approval_records';

    protected $fillable = [
        'tenant_id',
        'resource_type',
        'resource_id',
        'suggestion_type',
        'suggestion_payload',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'suggestion_payload' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }
}
