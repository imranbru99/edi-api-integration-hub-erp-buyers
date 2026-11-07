<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdiTransactionEvent extends Model
{
    protected $table = 'edi_transaction_events';

    protected $fillable = [
        'edi_transaction_id',
        'event',
        'status',
        'level',
        'message',
        'context',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
        ];
    }

    public function ediTransaction(): BelongsTo
    {
        return $this->belongsTo(EdiTransaction::class);
    }
}
