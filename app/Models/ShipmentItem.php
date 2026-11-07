<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShipmentItem extends Model
{
    protected $table = 'shipment_items';

    protected $fillable = [
        'shipment_id',
        'line_number',
        'sku',
        'quantity_shipped',
    ];

    protected function casts(): array
    {
        return [
            'quantity_shipped' => 'decimal:4',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
