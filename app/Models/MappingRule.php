<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingRule extends Model
{
    protected $table = 'mapping_rules';

    protected $fillable = [
        'mapping_profile_id',
        'mapping_version_id',
        'source_path',
        'target_path',
        'transform',
        'condition',
        'default_value',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'condition' => 'array',
        ];
    }

    public function mappingProfile(): BelongsTo
    {
        return $this->belongsTo(MappingProfile::class);
    }

    public function mappingVersion(): BelongsTo
    {
        return $this->belongsTo(MappingVersion::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toMappingArray(): array
    {
        return [
            'source_path' => $this->source_path,
            'target_path' => $this->target_path,
            'transform' => $this->transform,
            'default' => $this->default_value,
            'conditions' => $this->condition ?? [],
        ];
    }
}
