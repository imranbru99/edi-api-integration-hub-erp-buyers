<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingVersion extends Model
{
    protected $table = 'mapping_versions';

    protected $fillable = [
        'mapping_profile_id',
        'version',
        'status',
        'notes',
        'created_by',
    ];

    public function mappingProfile(): BelongsTo
    {
        return $this->belongsTo(MappingProfile::class);
    }
}
