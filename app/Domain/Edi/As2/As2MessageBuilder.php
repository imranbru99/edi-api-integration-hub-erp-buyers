<?php

declare(strict_types=1);

namespace App\Domain\Edi\As2;

final class As2MessageBuilder
{
    /**
     * Build a minimal AS2 MIME envelope for sandbox/testing (not a full RFC 4130 implementation).
     *
     * @param  array<string, mixed>  $options
     */
    public function build(
        string $ediPayload,
        string $as2From,
        string $as2To,
        string $subject = 'EDI Message',
        array $options = [],
    ): string {
        $messageId = $options['message_id'] ?? bin2hex(random_bytes(16)).'@edi-hub.local';
        $contentType = $options['content_type'] ?? 'application/edi-x12';
        $encoding = $options['content_transfer_encoding'] ?? 'binary';

        $headers = [
            "MIME-Version: 1.0",
            "AS2-From: {$as2From}",
            "AS2-To: {$as2To}",
            "Subject: {$subject}",
            "Message-ID: <{$messageId}>",
            "Content-Type: {$contentType}; charset=US-ASCII",
            "Content-Transfer-Encoding: {$encoding}",
        ];

        if (! empty($options['signed'])) {
            $headers[] = 'Content-Disposition: attachment; filename="edi.dat"';
        }

        return implode("\r\n", $headers)."\r\n\r\n".$ediPayload;
    }
}
