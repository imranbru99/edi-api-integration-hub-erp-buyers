<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use BelongsToTenant;

    protected $table = 'workflows';

    protected $fillable = [
        'tenant_id',
        'name',
        'trigger_event',
        'is_active',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('step_order');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(WorkflowExecution::class);
    }
}
