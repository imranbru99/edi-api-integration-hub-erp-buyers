<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $rawKey = $request->bearerToken()
            ?? $request->header('X-Api-Key')
            ?? $request->query('api_key');

        if (! $rawKey) {
            return response()->json(['message' => 'API key required.'], 401);
        }

        $prefix = substr($rawKey, 0, 12);
        $apiKey = ApiKey::query()
            ->where('key_prefix', $prefix)
            ->whereNull('revoked_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with('apiClient')
            ->get()
            ->first(fn (ApiKey $key) => Hash::check($rawKey, $key->key_hash));

        if (! $apiKey || ! $apiKey->apiClient?->is_active) {
            return response()->json(['message' => 'Invalid API key.'], 401);
        }

        $apiKey->last_used_at = now();
        $apiKey->save();

        TenantContext::setById($apiKey->apiClient->tenant_id);
        $request->attributes->set('api_client', $apiKey->apiClient);
        $request->attributes->set('api_key', $apiKey);

        $started = microtime(true);
        /** @var Response $response */
        $response = $next($request);

        ApiRequestLog::withoutGlobalScopes()->create([
            'api_client_id' => $apiKey->api_client_id,
            'tenant_id' => $apiKey->apiClient->tenant_id,
            'method' => $request->method(),
            'path' => '/'.$request->path(),
            'response_code' => $response->getStatusCode(),
            'duration_ms' => (int) ((microtime(true) - $started) * 1000),
            'ip_address' => $request->ip(),
        ]);

        return $response;
    }
}
