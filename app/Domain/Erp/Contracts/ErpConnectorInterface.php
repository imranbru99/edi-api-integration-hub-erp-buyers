<?php

namespace App\Domain\Erp\Contracts;

interface ErpConnectorInterface
{
    public function connect(array $config): void;

    public function testConnection(): bool;

    public function authenticate(): bool;

    public function fetchData(string $entity, array $filters = []): array;

    public function sendData(string $entity, array $payload): array;

    public function transformInbound(array $payload): array;

    public function transformOutbound(array $canonical): array;

    public function getCapabilities(): array;

    public function getHealth(): array;

    public function disconnect(): void;
}
