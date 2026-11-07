<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiClient extends Model
{
    use BelongsToTenant;

    protected $table = 'api_clients';

    protected $fillable = [
        'tenant_id',
        'name',
        'client_id',
        'client_secret_hash',
        'scopes',
        'rate_limit',
        'is_active',
    ];

    protected $hidden = [
        'client_secret_hash',
    ];

    protected function casts(): array
    {
        return [
            'scopes' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function keys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }
}
