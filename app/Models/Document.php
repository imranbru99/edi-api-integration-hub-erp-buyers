<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use BelongsToTenant;

    protected $table = 'documents';

    protected $fillable = [
        'tenant_id',
        'title',
        'document_type',
        'storage_path',
        'meta',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'archived_at' => 'datetime',
        ];
    }
}
