<?php

namespace App\Services\Billing;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\UsageRecord;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class UsageMeteringService
{
    public function record(Tenant $tenant, string $metric, int $quantity = 1, ?Carbon $periodStart = null, ?Carbon $periodEnd = null): UsageRecord
    {
        $periodStart ??= now()->startOfMonth();
        $periodEnd ??= now()->endOfMonth();

        $existing = UsageRecord::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('metric', $metric)
            ->where('period_start', $periodStart)
            ->where('period_end', $periodEnd)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);

            return $existing->fresh();
        }

        return UsageRecord::query()->create([
            'tenant_id' => $tenant->id,
            'metric' => $metric,
            'quantity' => $quantity,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ]);
    }

    public function getUsage(Tenant $tenant, string $metric, ?Carbon $periodStart = null, ?Carbon $periodEnd = null): int
    {
        $periodStart ??= now()->startOfMonth();
        $periodEnd ??= now()->endOfMonth();

        return (int) UsageRecord::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('metric', $metric)
            ->where('period_start', $periodStart)
            ->where('period_end', $periodEnd)
            ->value('quantity') ?? 0;
    }

    public function withinQuota(Tenant $tenant, string $metric, int $additional = 1): bool
    {
        $limit = $this->resolveLimit($tenant, $metric);

        if ($limit === null) {
            return true;
        }

        $current = $this->getUsage($tenant, $metric);

        return ($current + $additional) <= $limit;
    }

    public function resolveLimit(Tenant $tenant, string $metric): ?int
    {
        $subscription = Subscription::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', ['active', 'trialing'])
            ->latest()
            ->first();

        if ($subscription === null) {
            return null;
        }

        $plan = Plan::query()->find($subscription->plan_id);
        $limits = $plan?->limits ?? [];

        $value = Arr::get($limits, $metric);

        return is_numeric($value) ? (int) $value : null;
    }
}
