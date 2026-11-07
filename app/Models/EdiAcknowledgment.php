<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdiAcknowledgment extends Model
{
    protected $table = 'edi_acknowledgments';

    protected $fillable = [
        'edi_transaction_id',
        'ack_type',
        'standard',
        'document_type',
        'status',
        'payload',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function ediTransaction(): BelongsTo
    {
        return $this->belongsTo(EdiTransaction::class);
    }
}
