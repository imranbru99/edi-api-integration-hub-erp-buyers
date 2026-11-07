<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_headquarters',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_headquarters' => 'boolean',
        ];
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}
