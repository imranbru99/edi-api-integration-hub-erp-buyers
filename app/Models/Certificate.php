<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    use BelongsToTenant;

    protected $table = 'certificates';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'fingerprint',
        'not_before',
        'not_after',
        'public_cert_path',
        'private_key_encrypted',
        'status',
        'meta',
    ];

    protected $hidden = [
        'private_key_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'not_before' => 'datetime',
            'not_after' => 'datetime',
        ];
    }
}
