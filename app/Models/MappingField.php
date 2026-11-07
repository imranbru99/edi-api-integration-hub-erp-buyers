<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingField extends Model
{
    protected $table = 'mapping_fields';

    protected $fillable = [
        'mapping_version_id',
        'field_key',
        'data_type',
        'required',
        'constraints',
    ];

    protected function casts(): array
    {
        return [
            'constraints' => 'array',
            'required' => 'boolean',
        ];
    }

    public function mappingVersion(): BelongsTo
    {
        return $this->belongsTo(MappingVersion::class);
    }
}
