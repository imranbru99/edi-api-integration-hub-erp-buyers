<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiUsageLog extends Model
{
    use BelongsToTenant;

    protected $table = 'ai_usage_logs';

    protected $fillable = [
        'tenant_id',
        'feature',
        'tokens_in',
        'tokens_out',
        'cost',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'cost' => 'decimal:6',
        ];
    }
}
