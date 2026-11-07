<?php

namespace App\Services\Workflow;

use App\Models\Workflow;
use App\Models\WorkflowExecution;
use Illuminate\Support\Arr;

class WorkflowEngine
{
    public function trigger(string $event, array $context = []): array
    {
        $workflows = Workflow::query()
            ->where('is_active', true)
            ->where('trigger_event', $event)
            ->with('steps')
            ->get();

        $results = [];
        foreach ($workflows as $workflow) {
            $results[] = $this->execute($workflow, $context);
        }

        return $results;
    }

    public function execute(Workflow $workflow, array $context): array
    {
        $execution = WorkflowExecution::create([
            'workflow_id' => $workflow->id,
            'status' => 'running',
            'context' => $context,
            'started_at' => now(),
        ]);

        $log = [];
        foreach ($workflow->steps as $step) {
            if ($step->step_type === 'condition') {
                if (! $this->evaluateConditions($step->config ?? [], $context)) {
                    $log[] = ['step' => $step->id, 'skipped' => true];
                    continue;
                }
            }

            if ($step->step_type === 'action') {
                $log[] = ['step' => $step->id, 'result' => $this->runAction($step->config ?? [], $context)];
            }
        }

        $execution->status = 'completed';
        $execution->context = array_merge($context, ['_workflow_log' => $log]);
        $execution->finished_at = now();
        $execution->save();

        return ['execution_id' => $execution->id, 'log' => $log];
    }

    protected function evaluateConditions(array $config, array $context): bool
    {
        $conditions = $config['conditions'] ?? [];
        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? 'equals';
            $expected = $condition['value'] ?? null;
            $actual = Arr::get($context, $field);

            $ok = match ($operator) {
                'equals' => $actual == $expected,
                'not_equals' => $actual != $expected,
                'contains' => is_string($actual) && str_contains($actual, (string) $expected),
                default => false,
            };

            if (! $ok) {
                return false;
            }
        }

        return true;
    }

    protected function runAction(array $config, array $context): array
    {
        $action = $config['action'] ?? 'noop';

        return match ($action) {
            'set_context' => ['patched' => array_merge($context, $config['values'] ?? [])],
            'dispatch_webhook' => ['queued_event' => $config['event'] ?? 'workflow.action'],
            default => ['action' => $action, 'status' => 'noop'],
        };
    }
}
