<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdiEnvelope extends Model
{
    protected $table = 'edi_envelopes';

    protected $fillable = [
        'edi_transaction_id',
        'envelope_type',
        'sender_id',
        'receiver_id',
        'control_number',
        'headers',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
        ];
    }

    public function ediTransaction(): BelongsTo
    {
        return $this->belongsTo(EdiTransaction::class);
    }
}
