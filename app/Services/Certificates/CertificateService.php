<?php

namespace App\Services\Certificates;

use App\Models\Certificate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    public function store(array $data, ?UploadedFile $publicCert = null, ?string $privateKeyPlain = null): Certificate
    {
        $path = $data['public_cert_path'] ?? null;
        if ($publicCert) {
            $path = $publicCert->store('certificates', 'local');
        }

        $certificate = Certificate::create([
            'tenant_id' => $data['tenant_id'],
            'name' => $data['name'],
            'type' => $data['type'] ?? 'x509',
            'fingerprint' => $data['fingerprint'] ?? null,
            'not_before' => $data['not_before'] ?? null,
            'not_after' => $data['not_after'] ?? null,
            'public_cert_path' => $path,
            'private_key_encrypted' => $privateKeyPlain ? Crypt::encryptString($privateKeyPlain) : null,
            'status' => $data['status'] ?? 'active',
            'meta' => $data['meta'] ?? null,
        ]);

        return $certificate->fresh();
    }

    public function publicCertificateContents(Certificate $certificate): ?string
    {
        if (! $certificate->public_cert_path) {
            return null;
        }

        return Storage::disk('local')->get($certificate->public_cert_path) ?: null;
    }

    public function rotatePrivateKey(Certificate $certificate, string $privateKeyPlain): Certificate
    {
        $certificate->private_key_encrypted = Crypt::encryptString($privateKeyPlain);
        $certificate->save();

        return $certificate;
    }
}
