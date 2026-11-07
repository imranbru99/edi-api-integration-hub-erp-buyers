<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Edi\Services\EdiInboundProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessInboundEdiJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public readonly string $rawContent,
        public readonly int $tenantId,
        public readonly int $tradingPartnerId,
        public readonly ?string $documentTypeHint = null,
        public readonly array $context = [],
    ) {
        $this->onQueue('edi-inbound');
    }

    public function handle(EdiInboundProcessor $processor): void
    {
        $processor->process(
            $this->rawContent,
            $this->tenantId,
            $this->tradingPartnerId,
            $this->documentTypeHint,
            $this->context,
        );
    }
}
