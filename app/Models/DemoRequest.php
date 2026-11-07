<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemoRequest extends Model
{
    protected $table = 'demo_requests';

    protected $fillable = [
        'company',
        'email',
        'name',
        'notes',
        'status',
    ];
}
