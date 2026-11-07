<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingApproval extends Model
{
    protected $table = 'mapping_approvals';

    protected $fillable = [
        'mapping_version_id',
        'approved_by',
        'status',
        'comments',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function mappingVersion(): BelongsTo
    {
        return $this->belongsTo(MappingVersion::class);
    }
}
