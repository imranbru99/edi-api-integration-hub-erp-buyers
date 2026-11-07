<?php

declare(strict_types=1);

namespace App\Domain\Edi\Delivery;

use App\Domain\Edi\As2\As2MessageBuilder;
use App\Domain\Edi\Contracts\DeliveryGatewayInterface;
use App\Models\Certificate;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Sandbox AS2 delivery gateway.
 *
 * Does not open real HTTP/MDN endpoints. When signing material is present, attempts PKCS#7 signing;
 * otherwise writes the outbound MIME body to storage/app/as2-out/ and reports success.
 */
final class As2DeliveryGateway implements DeliveryGatewayInterface
{
    public function __construct(
        private readonly As2MessageBuilder $messageBuilder,
    ) {}

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
        $as2From = (string) ($metadata['as2_from'] ?? 'EDI-HUB-SENDER');
        $as2To = (string) ($metadata['as2_to'] ?? 'PARTNER-RECEIVER');
        $subject = (string) ($metadata['subject'] ?? "AS2 {$documentType}");

        $mime = $this->messageBuilder->build($payload, $as2From, $as2To, $subject);

        $signed = false;
        $certificateId = $metadata['certificate_id'] ?? null;

        if ($certificateId) {
            $cert = Certificate::query()->withoutGlobalScopes()->find($certificateId);
            if ($cert?->private_key_encrypted && $cert->public_cert_path) {
                $mime = $this->attemptSign($mime, $cert, $signed);
            }
        }

        $filename = sprintf(
            'as2-out/tenant-%d/partner-%d/%s-%s.as2',
            $tenantId,
            $tradingPartnerId,
            now()->format('Ymd-His'),
            bin2hex(random_bytes(4)),
        );

        Storage::disk('local')->put($filename, $mime);

        Log::info('AS2 sandbox delivery stored', [
            'path' => $filename,
            'tenant_id' => $tenantId,
            'trading_partner_id' => $tradingPartnerId,
            'document_type' => $documentType,
            'signed' => $signed,
            'sandbox' => true,
        ]);

        return [
            'success' => true,
            'delivery_id' => 'as2-sandbox-'.bin2hex(random_bytes(6)),
            'message' => 'AS2 message written to local sandbox storage (not sent over HTTP).',
            'path' => $filename,
            'signed' => $signed,
            'sandbox' => true,
        ];
    }

    private function attemptSign(string $mime, Certificate $certificate, bool &$signed): string
    {
        try {
            $privateKey = Crypt::decryptString($certificate->private_key_encrypted);
            $certPath = Storage::disk('local')->path($certificate->public_cert_path);
            $tmpIn = tempnam(sys_get_temp_dir(), 'as2-in-');
            $tmpOut = tempnam(sys_get_temp_dir(), 'as2-out-');
            $tmpKey = tempnam(sys_get_temp_dir(), 'as2-key-');

            file_put_contents($tmpIn, $mime);
            file_put_contents($tmpKey, $privateKey);

            if (@openssl_pkcs7_sign($tmpIn, $tmpOut, 'file://'.$certPath, ['file://'.$tmpKey, ''], [], PKCS7_DETACHED)) {
                $signedBody = file_get_contents($tmpOut);
                if ($signedBody !== false && $signedBody !== '') {
                    $signed = true;
                    $mime = $signedBody;
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('AS2 sandbox signing skipped', ['error' => $exception->getMessage()]);
        } finally {
            foreach ([$tmpIn ?? null, $tmpOut ?? null, $tmpKey ?? null] as $file) {
                if (is_string($file) && file_exists($file)) {
                    @unlink($file);
                }
            }
        }

        return $mime;
    }
}
