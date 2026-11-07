<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faq extends Model
{
    use BelongsToTenant;

    protected $table = 'faqs';

    protected $fillable = [
        'tenant_id',
        'question',
        'answer',
        'sort_order',
    ];
}
