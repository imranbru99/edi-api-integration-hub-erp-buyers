<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiConversation extends Model
{
    use BelongsToTenant;

    protected $table = 'ai_conversations';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'subject',
        'messages',
    ];

    protected function casts(): array
    {
        return [
            'messages' => 'array',
        ];
    }
}
