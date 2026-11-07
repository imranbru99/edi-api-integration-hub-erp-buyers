<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'limits',
        'modules',
        'price_monthly',
        'price_yearly',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'limits' => 'array',
            'modules' => 'array',
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
