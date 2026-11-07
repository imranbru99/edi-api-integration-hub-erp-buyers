<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdiDocumentType extends Model
{
    protected $table = 'edi_document_types';

    protected $fillable = [
        'edi_standard_id',
        'code',
        'name',
        'direction',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function ediStandard(): BelongsTo
    {
        return $this->belongsTo(EdiStandard::class);
    }
}
