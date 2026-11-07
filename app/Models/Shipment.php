<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use BelongsToTenant;

    protected $table = 'shipments';

    protected $fillable = [
        'tenant_id',
        'trading_partner_id',
        'purchase_order_id',
        'edi_transaction_id',
        'shipment_number',
        'status',
        'shipped_at',
        'carrier',
        'tracking_number',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'shipped_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
