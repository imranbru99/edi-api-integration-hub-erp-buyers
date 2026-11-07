<?php

namespace App\Console\Commands;

use App\Domain\Edi\Services\EdiInboundProcessor;
use App\Models\EdiTransaction;
use App\Models\Tenant;
use App\Models\TradingPartner;
use App\Support\TenantContext;
use Illuminate\Console\Command;

class ProcessEdiInboundCommand extends Command
{
    protected $signature = 'edi-hub:process-inbound
        {file : Path to raw EDI file on disk}
        {--tenant= : Tenant ID (defaults to first active tenant)}
        {--partner= : Trading partner ID (defaults to first partner for tenant)}
        {--type= : Optional document type hint (e.g. 850)}';

    protected $description = 'Parse, validate, map, and ack an inbound EDI file (CLI test harness)';

    public function handle(EdiInboundProcessor $processor): int
    {
        $path = $this->argument('file');
        if (! is_readable($path)) {
            $this->error("File not readable: {$path}");

            return self::FAILURE;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            $this->error('Unable to read file contents.');

            return self::FAILURE;
        }

        $tenantId = $this->option('tenant');
        $tenant = $tenantId
            ? Tenant::query()->find($tenantId)
            : Tenant::query()->where('status', 'active')->orderBy('id')->first();

        if (! $tenant) {
            $this->error('No tenant found. Pass --tenant=');

            return self::FAILURE;
        }

        TenantContext::set($tenant);

        $partnerId = $this->option('partner');
        $partner = $partnerId
            ? TradingPartner::query()->where('tenant_id', $tenant->id)->find($partnerId)
            : TradingPartner::query()->where('tenant_id', $tenant->id)->orderBy('id')->first();

        if (! $partner) {
            $this->error('No trading partner found for tenant. Pass --partner=');

            return self::FAILURE;
        }

        $transaction = $processor->process(
            $raw,
            $tenant->id,
            $partner->id,
            $this->option('type'),
        );

        $this->table(
            ['Field', 'Value'],
            [
                ['Transaction ID', $transaction->id],
                ['Status', $transaction->status],
                ['Document type', $transaction->document_type],
                ['Control #', $transaction->control_number],
                ['Acknowledgment ID', $transaction->acknowledgment_id],
            ],
        );

        if ($transaction->status === EdiInboundProcessor::STATUS_FAILED) {
            $this->error($transaction->error_message ?? 'Processing failed.');

            return self::FAILURE;
        }

        $this->info('Inbound processing finished.');

        return self::SUCCESS;
    }
}
