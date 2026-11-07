<?php

namespace App\Http\Middleware;

use App\Models\Module;
use App\Models\TenantModule;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantModule
{
    public function handle(Request $request, Closure $next, string $moduleCode): Response
    {
        $tenant = TenantContext::get();

        if ($tenant === null) {
            abort(403, 'Tenant context required.');
        }

        $module = Module::query()->where('code', $moduleCode)->first();

        if ($module === null) {
            abort(404, 'Module not found.');
        }

        $active = TenantModule::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('module_id', $module->id)
            ->where('is_active', true)
            ->exists();

        if (! $active) {
            abort(403, 'Module is not active for this tenant.');
        }

        return $next($request);
    }
}
