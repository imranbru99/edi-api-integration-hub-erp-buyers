<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryRecord extends Model
{
    use BelongsToTenant;

    protected $table = 'inventory_records';

    protected $fillable = [
        'tenant_id',
        'product_id',
        'location_code',
        'quantity_on_hand',
        'quantity_reserved',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'synced_at' => 'datetime',
            'quantity_on_hand' => 'decimal:4',
            'quantity_reserved' => 'decimal:4',
        ];
    }
}
