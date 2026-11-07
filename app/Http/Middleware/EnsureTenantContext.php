<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (TenantContext::getId() === null && $request->user()?->current_tenant_id) {
            TenantContext::setById((int) $request->user()->current_tenant_id);
        }

        return $next($request);
    }
}
