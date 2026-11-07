<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingTestCase extends Model
{
    protected $table = 'mapping_test_cases';

    protected $fillable = [
        'mapping_version_id',
        'name',
        'input_payload',
        'expected_output',
        'last_result',
    ];

    protected function casts(): array
    {
        return [
            'input_payload' => 'array',
            'expected_output' => 'array',
        ];
    }

    public function mappingVersion(): BelongsTo
    {
        return $this->belongsTo(MappingVersion::class);
    }
}
