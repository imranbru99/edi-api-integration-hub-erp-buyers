<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingProfile extends Model
{
    use BelongsToTenant;

    protected $table = 'mapping_profiles';

    protected $fillable = [
        'tenant_id',
        'trading_partner_id',
        'name',
        'standard',
        'document_type',
        'direction',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tradingPartner(): BelongsTo
    {
        return $this->belongsTo(TradingPartner::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(MappingVersion::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(MappingRule::class);
    }
}
