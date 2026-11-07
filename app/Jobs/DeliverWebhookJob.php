<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Services\Webhooks\WebhookDispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(public int $deliveryId) {}

    public function handle(WebhookDispatcher $dispatcher): void
    {
        $delivery = WebhookDelivery::query()->find($this->deliveryId);
        if (! $delivery) {
            return;
        }

        $endpoint = WebhookEndpoint::query()->find($delivery->webhook_endpoint_id);
        if (! $endpoint || ! $endpoint->is_active) {
            $delivery->status = 'failed';
            $delivery->save();

            return;
        }

        $payload = json_encode($delivery->payload ?? [], JSON_THROW_ON_ERROR);
        $secret = $this->resolveSigningSecret($endpoint);
        $signature = WebhookDispatcher::signPayload($secret, $payload);

        $response = Http::timeout(15)
            ->withHeaders([
                'X-EDI-Hub-Event' => $delivery->event,
                'X-EDI-Hub-Signature' => $signature,
                'Content-Type' => 'application/json',
            ])
            ->post($endpoint->url, $delivery->payload ?? []);

        $delivery->attempts++;
        $delivery->response_code = $response->status();

        if ($response->successful()) {
            $delivery->status = 'delivered';
            $delivery->next_retry_at = null;
            $delivery->save();

            return;
        }

        if ($delivery->attempts >= $this->tries) {
            $delivery->status = 'failed';
            $delivery->save();

            return;
        }

        $dispatcher->scheduleRetry($delivery, $response->status());
    }

    protected function resolveSigningSecret(WebhookEndpoint $endpoint): string
    {
        if (! empty($endpoint->getAttributes()['secret_plain'])) {
            return (string) $endpoint->getAttributes()['secret_plain'];
        }

        try {
            return Crypt::decryptString($endpoint->secret_hash);
        } catch (\Throwable) {
            return (string) $endpoint->secret_hash;
        }
    }
}
