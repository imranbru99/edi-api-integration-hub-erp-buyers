<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdiValidationRule extends Model
{
    use BelongsToTenant;

    protected $table = 'edi_validation_rules';

    protected $fillable = [
        'tenant_id',
        'standard',
        'document_type',
        'rule_code',
        'severity',
        'definition',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'definition' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
