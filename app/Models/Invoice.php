<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use BelongsToTenant;

    protected $table = 'invoices';

    protected $fillable = [
        'tenant_id',
        'trading_partner_id',
        'purchase_order_id',
        'edi_transaction_id',
        'invoice_number',
        'status',
        'total_amount',
        'currency',
        'invoice_date',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'invoice_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
