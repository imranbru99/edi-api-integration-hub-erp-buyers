<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TradingPartner extends Model
{
    use BelongsToTenant;

    protected $table = 'trading_partners';

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'partner_type',
        'status',
        'edi_id',
        'isa_id',
        'gs_id',
        'edifact_id',
        'country',
        'timezone',
        'currency',
        'default_standard',
        'communication_method',
        'settings',
        'onboarding_step',
        'is_demo',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_demo' => 'boolean',
            'onboarding_step' => 'integer',
        ];
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(PartnerContact::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(PartnerAddress::class);
    }

    public function identifiers(): HasMany
    {
        return $this->hasMany(PartnerIdentifier::class);
    }

    public function documentTypes(): HasMany
    {
        return $this->hasMany(PartnerDocumentType::class);
    }

    public function connections(): HasMany
    {
        return $this->hasMany(PartnerConnection::class);
    }

    public function ediTransactions(): HasMany
    {
        return $this->hasMany(EdiTransaction::class);
    }
}
