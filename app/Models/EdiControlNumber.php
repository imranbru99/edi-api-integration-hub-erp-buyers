<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdiControlNumber extends Model
{
    use BelongsToTenant;

    protected $table = 'edi_control_numbers';

    protected $fillable = [
        'tenant_id',
        'trading_partner_id',
        'standard',
        'number_type',
        'counter_type',
        'last_value',
    ];
}
