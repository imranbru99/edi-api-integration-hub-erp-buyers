<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerAddress extends Model
{
    protected $table = 'partner_addresses';

    protected $fillable = [
        'trading_partner_id',
        'type',
        'line1',
        'line2',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    public function tradingPartner(): BelongsTo
    {
        return $this->belongsTo(TradingPartner::class);
    }
}
