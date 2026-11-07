<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdiTransaction extends Model
{
    use BelongsToTenant;

    protected $table = 'edi_transactions';

    protected $fillable = [
        'tenant_id',
        'trading_partner_id',
        'standard',
        'document_type',
        'direction',
        'control_number',
        'interchange_control_number',
        'group_control_number',
        'transaction_set_control_number',
        'status',
        'raw_payload',
        'parsed_payload',
        'canonical_payload',
        'raw_content',
        'parsed_content',
        'canonical_content',
        'transformed_content',
        'error_message',
        'idempotency_key',
        'acknowledgment_id',
        'checksum',
        'received_at',
        'processed_at',
        'acknowledged_at',
        'delivered_at',
        'delivery_attempts',
        'validation_errors_count',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'parsed_payload' => 'array',
            'canonical_payload' => 'array',
            'parsed_content' => 'array',
            'canonical_content' => 'array',
            'transformed_content' => 'array',
            'meta' => 'array',
            'received_at' => 'datetime',
            'processed_at' => 'datetime',
            'acknowledged_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function tradingPartner(): BelongsTo
    {
        return $this->belongsTo(TradingPartner::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(EdiTransactionEvent::class)->orderBy('created_at');
    }

    public function purchaseOrder(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    public function acknowledgment(): BelongsTo
    {
        return $this->belongsTo(EdiAcknowledgment::class, 'acknowledgment_id');
    }

    public function errors(): HasMany
    {
        return $this->hasMany(TransactionError::class);
    }

    public function retries(): HasMany
    {
        return $this->hasMany(TransactionRetry::class);
    }
}
