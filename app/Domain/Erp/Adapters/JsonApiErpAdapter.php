<?php

namespace App\Domain\Erp\Adapters;

use App\Domain\Erp\Contracts\ErpConnectorInterface;
use Illuminate\Support\Facades\Http;

class JsonApiErpAdapter implements ErpConnectorInterface
{
    protected array $config = [];

    public function connect(array $config): void
    {
        $this->config = $config;
    }

    public function testConnection(): bool
    {
        $response = $this->request('GET', '/health');

        return $response !== null && $response->successful();
    }

    public function authenticate(): bool
    {
        if (empty($this->config['token'])) {
            return empty($this->config['auth_url']);
        }

        return true;
    }

    public function fetchData(string $entity, array $filters = []): array
    {
        $response = $this->request('GET', '/'.$entity, $filters);
        if (! $response?->successful()) {
            return [];
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    public function sendData(string $entity, array $payload): array
    {
        $response = $this->request('POST', '/'.$entity, $payload);
        if (! $response?->successful()) {
            return ['accepted' => false, 'status' => $response?->status()];
        }

        return $response->json() ?? ['accepted' => true];
    }

    public function transformInbound(array $payload): array
    {
        return $payload;
    }

    public function transformOutbound(array $canonical): array
    {
        return ['data' => $canonical];
    }

    public function getCapabilities(): array
    {
        return ['entities' => ['*'], 'transport' => 'rest_json'];
    }

    public function getHealth(): array
    {
        $ok = $this->testConnection();

        return ['status' => $ok ? 'healthy' : 'unreachable'];
    }

    public function disconnect(): void
    {
        $this->config = [];
    }

    protected function request(string $method, string $path, array $data = [])
    {
        $base = rtrim((string) ($this->config['base_url'] ?? ''), '/');
        if ($base === '') {
            return null;
        }

        $request = Http::timeout((int) ($this->config['timeout'] ?? 30))
            ->acceptJson()
            ->withToken((string) ($this->config['token'] ?? ''));

        $url = $base.$path;

        return match (strtoupper($method)) {
            'GET' => $request->get($url, $data),
            'POST' => $request->post($url, $data),
            'PUT' => $request->put($url, $data),
            default => $request->send($method, $url, ['json' => $data]),
        };
    }
}
