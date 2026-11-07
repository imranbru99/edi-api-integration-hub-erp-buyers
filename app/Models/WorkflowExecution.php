<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowExecution extends Model
{
    protected $table = 'workflow_executions';

    protected $fillable = [
        'workflow_id',
        'status',
        'context',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }
}
