<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiRequestLog extends Model
{
    protected $table = 'api_request_logs';

    protected $fillable = [
        'api_client_id',
        'tenant_id',
        'method',
        'path',
        'response_code',
        'duration_ms',
        'ip_address',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
