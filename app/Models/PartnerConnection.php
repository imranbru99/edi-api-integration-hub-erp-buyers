<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerConnection extends Model
{
    protected $table = 'partner_connections';

    protected $fillable = [
        'trading_partner_id',
        'protocol',
        'host',
        'port',
        'credentials_encrypted',
        'directories',
        'status',
    ];

    protected $hidden = [
        'credentials_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'directories' => 'array',
        ];
    }

    public function tradingPartner(): BelongsTo
    {
        return $this->belongsTo(TradingPartner::class);
    }
}
