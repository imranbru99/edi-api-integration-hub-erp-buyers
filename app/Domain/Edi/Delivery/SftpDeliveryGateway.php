<?php

declare(strict_types=1);

namespace App\Domain\Edi\Delivery;

use App\Domain\Edi\Contracts\DeliveryGatewayInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class SftpDeliveryGateway implements DeliveryGatewayInterface
{
    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    public function deliver(
        string $payload,
        int $tenantId,
        int $tradingPartnerId,
        string $documentType,
        array $metadata = [],
    ): array {
        $disk = (string) ($metadata['disk'] ?? 'sftp');
        $path = (string) ($metadata['path'] ?? sprintf(
            'out/%d/%d/%s_%s.edi',
            $tenantId,
            $tradingPartnerId,
            $documentType,
            Str::uuid()->toString()
        ));

        try {
            if (config("filesystems.disks.{$disk}") !== null) {
                Storage::disk($disk)->put($path, $payload);

                return [
                    'success' => true,
                    'disk' => $disk,
                    'path' => $path,
                ];
            }
        } catch (\Throwable) {
            // Fall through to local sandbox path.
        }

        $localPath = 'sftp-out/'.$path;
        Storage::disk('local')->put($localPath, $payload);

        return [
            'success' => true,
            'disk' => 'local',
            'path' => $localPath,
            'message' => 'SFTP disk unavailable; wrote to local sandbox.',
        ];
    }
}
