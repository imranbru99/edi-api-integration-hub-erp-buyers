<?php

namespace Tests\Feature;

use App\Services\Webhooks\WebhookDispatcher;
use Tests\TestCase;

class WebhookSignatureTest extends TestCase
{
    public function test_payload_signature_is_deterministic_hmac_sha256(): void
    {
        $secret = 'webhook-secret-123';
        $payload = json_encode(['event' => 'edi.transaction.completed', 'id' => 42], JSON_THROW_ON_ERROR);

        $expected = hash_hmac('sha256', $payload, $secret);

        $this->assertSame($expected, WebhookDispatcher::signPayload($secret, $payload));
    }
}
