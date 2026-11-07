<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreWebhookEndpointRequest;
use App\Http\Resources\Api\V1\WebhookEndpointResource;
use App\Models\WebhookEndpoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WebhookController extends Controller
{
    public function index(): JsonResponse
    {
        return WebhookEndpointResource::collection(WebhookEndpoint::query()->latest()->get())->response();
    }

    public function store(StoreWebhookEndpointRequest $request): JsonResponse
    {
        $plain = $request->validated('secret');
        $endpoint = WebhookEndpoint::create([
            'tenant_id' => $request->validated('tenant_id'),
            'url' => $request->validated('url'),
            'secret_hash' => Crypt::encryptString($plain),
            'events' => $request->validated('events') ?? ['*'],
            'is_active' => true,
        ]);

        return (new WebhookEndpointResource($endpoint))->additional(['secret' => $plain])->response()->setStatusCode(201);
    }

    public function destroy(WebhookEndpoint $webhookEndpoint): JsonResponse
    {
        $webhookEndpoint->delete();

        return response()->json(['deleted' => true]);
    }
}
