<?php

namespace App\Services\Webhooks;

use App\Jobs\DeliverWebhookJob;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\Hash;

class WebhookDispatcher
{
    public function dispatchEvent(int $tenantId, string $event, array $payload): void
    {
        $endpoints = WebhookEndpoint::query()
            ->where('is_active', true)
            ->get()
            ->filter(function (WebhookEndpoint $endpoint) use ($event) {
                $events = $endpoint->events ?? ['*'];

                return in_array('*', $events, true) || in_array($event, $events, true);
            });

        foreach ($endpoints as $endpoint) {
            $delivery = WebhookDelivery::create([
                'webhook_endpoint_id' => $endpoint->id,
                'event' => $event,
                'payload' => $payload,
                'status' => 'pending',
                'attempts' => 0,
            ]);

            DeliverWebhookJob::dispatch($delivery->id);
        }
    }

    public static function signPayload(string $secret, string $payload): string
    {
        return hash_hmac('sha256', $payload, $secret);
    }

    public static function verifySecret(WebhookEndpoint $endpoint, string $plainSecret): bool
    {
        return Hash::check($plainSecret, $endpoint->secret_hash);
    }

    public function scheduleRetry(WebhookDelivery $delivery, int $responseCode): void
    {
        $delivery->attempts++;
        $delivery->response_code = $responseCode;
        $delivery->status = 'retrying';
        $backoffMinutes = min(60, 2 ** $delivery->attempts);
        $delivery->next_retry_at = now()->addMinutes($backoffMinutes);
        $delivery->save();

        DeliverWebhookJob::dispatch($delivery->id)->delay($delivery->next_retry_at);
    }
}
