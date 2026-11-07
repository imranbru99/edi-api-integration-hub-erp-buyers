<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToTenant;

    protected $table = 'products';

    protected $fillable = [
        'tenant_id',
        'product_catalog_id',
        'sku',
        'name',
        'description',
        'unit_price',
        'uom',
        'attributes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'is_active' => 'boolean',
            'unit_price' => 'decimal:4',
        ];
    }
}
