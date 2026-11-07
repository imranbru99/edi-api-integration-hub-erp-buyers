<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactRequest extends Model
{
    protected $table = 'contact_requests';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
    ];
}
