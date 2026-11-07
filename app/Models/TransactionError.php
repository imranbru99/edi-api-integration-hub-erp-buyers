<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TransactionError extends Model
{
    use BelongsToTenant;

    protected $table = 'transaction_errors';

    protected $fillable = [
        'tenant_id',
        'edi_transaction_id',
        'error_code',
        'severity',
        'message',
        'context',
        'resolved',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'resolved' => 'boolean',
        ];
    }

    public function ediTransaction(): BelongsTo
    {
        return $this->belongsTo(EdiTransaction::class);
    }
}
