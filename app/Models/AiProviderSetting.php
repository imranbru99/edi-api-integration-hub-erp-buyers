<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProviderSetting extends Model
{
    use BelongsToTenant;

    protected $table = 'ai_provider_settings';

    protected $fillable = [
        'tenant_id',
        'provider',
        'model',
        'enabled',
        'authorized',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'enabled' => 'boolean',
            'authorized' => 'boolean',
        ];
    }
}
