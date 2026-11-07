<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use BelongsToTenant;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'tenant_id',
        'trading_partner_id',
        'edi_transaction_id',
        'po_number',
        'status',
        'order_date',
        'total_amount',
        'currency',
        'ship_to',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'ship_to' => 'array',
            'meta' => 'array',
            'order_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function tradingPartner(): BelongsTo
    {
        return $this->belongsTo(TradingPartner::class);
    }

    public function ediTransaction(): BelongsTo
    {
        return $this->belongsTo(EdiTransaction::class);
    }
}
