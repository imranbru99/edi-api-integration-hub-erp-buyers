<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionRetry extends Model
{
    protected $table = 'transaction_retries';

    protected $fillable = [
        'edi_transaction_id',
        'attempt',
        'status',
        'scheduled_at',
        'executed_at',
        'result_message',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'executed_at' => 'datetime',
        ];
    }

    public function ediTransaction(): BelongsTo
    {
        return $this->belongsTo(EdiTransaction::class);
    }
}
