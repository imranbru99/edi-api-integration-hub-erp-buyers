<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantFromUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->current_tenant_id) {
            TenantContext::setById($user->current_tenant_id);
        } else {
            TenantContext::clear();
        }

        return $next($request);
    }
}
