<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Edi\Services\EdiOutboundProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessOutboundEdiJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $canonical
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        public readonly array $canonical,
        public readonly int $tenantId,
        public readonly int $tradingPartnerId,
        public readonly string $standard,
        public readonly string $documentType,
        public readonly array $options = [],
    ) {
        $this->onQueue('edi-outbound');
    }

    public function handle(EdiOutboundProcessor $processor): void
    {
        $processor->process(
            $this->canonical,
            $this->tenantId,
            $this->tradingPartnerId,
            $this->standard,
            $this->documentType,
            $this->options,
        );
    }
}
