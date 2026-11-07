<?php

namespace App\Services\Api;

use App\Models\ApiClient;
use App\Models\ApiKey;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiClientService
{
    public function createClient(array $data): array
    {
        $plainSecret = Str::random(40);
        $client = ApiClient::create([
            'tenant_id' => $data['tenant_id'],
            'name' => $data['name'],
            'client_id' => $data['client_id'] ?? 'cli_'.Str::lower(Str::random(16)),
            'client_secret_hash' => Hash::make($plainSecret),
            'scopes' => $data['scopes'] ?? ['*'],
            'rate_limit' => $data['rate_limit'] ?? 60,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return ['client' => $client, 'client_secret' => $plainSecret];
    }

    public function generateKey(ApiClient $client, ?\DateTimeInterface $expiresAt = null): array
    {
        $plainKey = 'edi_'.Str::lower(Str::random(48));
        $prefix = substr($plainKey, 0, 12);

        $key = ApiKey::create([
            'api_client_id' => $client->id,
            'key_prefix' => $prefix,
            'key_hash' => Hash::make($plainKey),
            'expires_at' => $expiresAt,
        ]);

        return ['key' => $key, 'plaintext' => $plainKey];
    }

    public function revokeKey(ApiKey $key): void
    {
        $key->revoked_at = now();
        $key->save();
    }
}
