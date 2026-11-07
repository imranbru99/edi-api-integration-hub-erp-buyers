<?php

declare(strict_types=1);

namespace App\Domain\Edi\Services;

use App\Domain\Edi\Contracts\DeliveryGatewayInterface;
use App\Domain\Edi\Generators\EdifactGenerator;
use App\Domain\Edi\Generators\X12Generator;
use App\Domain\Edi\Parsers\EdifactParser;
use App\Domain\Edi\Parsers\X12Parser;
use App\Domain\Edi\Mapping\MappingEngine;
use App\Models\EdiTransaction;
use App\Models\EdiTransactionEvent;
use App\Models\MappingProfile;
use Illuminate\Support\Facades\DB;

final class EdiOutboundProcessor
{
    public function __construct(
        private readonly X12Generator $x12Generator,
        private readonly EdifactGenerator $edifactGenerator,
        private readonly EdiValidationService $validationService,
        private readonly MappingEngine $mappingEngine,
        private readonly ControlNumberService $controlNumberService,
        private readonly DeliveryGatewayInterface $deliveryGateway,
        private readonly X12Parser $x12Parser,
        private readonly EdifactParser $edifactParser,
    ) {}

    /**
     * @param  array<string, mixed>  $canonical
     * @param  array<string, mixed>  $options
     */
    public function process(
        array $canonical,
        int $tenantId,
        int $tradingPartnerId,
        string $standard,
        string $documentType,
        array $options = [],
    ): EdiTransaction {
        return DB::transaction(function () use ($canonical, $tenantId, $tradingPartnerId, $standard, $documentType, $options): EdiTransaction {
            $transaction = EdiTransaction::query()->create([
                'tenant_id' => $tenantId,
                'trading_partner_id' => $tradingPartnerId,
                'direction' => 'outbound',
                'standard' => $standard,
                'document_type' => $documentType,
                'status' => 'received',
                'canonical_content' => $canonical,
            ]);

            $this->recordEvent($transaction, 'received', 'Outbound generation requested.');

            $mapped = $canonical;
            $profile = MappingProfile::query()
                ->where('tenant_id', $tenantId)
                ->where('trading_partner_id', $tradingPartnerId)
                ->where('direction', 'outbound')
                ->where('document_type', $documentType)
                ->with('rules')
                ->first();

            if ($profile !== null) {
                $transaction->update(['status' => 'mapping']);
                $rules = $profile->rules->map(fn ($rule) => $rule->toMappingArray())->all();
                $mapped = $this->mappingEngine->apply($canonical, $rules);
                $transaction->update(['transformed_content' => $mapped]);
            }

            $metadata = array_merge($options['metadata'] ?? [], [
                'sender_id' => $options['sender_id'] ?? 'SENDER',
                'receiver_id' => $options['receiver_id'] ?? 'RECEIVER',
                'isa_control' => $this->controlNumberService->nextIsa($tenantId, $tradingPartnerId),
                'gs_control' => $this->controlNumberService->nextGs($tenantId, $tradingPartnerId),
                'interchange_control' => $this->controlNumberService->nextInterchange($tenantId, $tradingPartnerId),
            ]);

            $transactionPayload = $this->buildTransactionPayload(
                $standard,
                $documentType,
                $mapped,
                $tenantId,
                $tradingPartnerId,
            );

            $raw = $standard === 'edifact'
                ? $this->edifactGenerator->generate(['metadata' => $metadata, 'transaction' => $transactionPayload])
                : $this->x12Generator->generate(['metadata' => $metadata, 'transaction' => $transactionPayload]);

            $transaction->update([
                'raw_content' => $raw,
                'control_number' => $transactionPayload['control_number'] ?? $transactionPayload['message_reference'] ?? null,
                'status' => 'validating',
            ]);

            $parsed = $standard === 'edifact'
                ? $this->edifactParser->parse($raw)
                : $this->x12Parser->parse($raw);

            $errors = $this->validationService->validate($standard, $parsed);
            if ($errors !== []) {
                $transaction->update([
                    'status' => 'failed',
                    'error_message' => implode(' ', $errors),
                    'parsed_content' => $parsed,
                ]);
                $this->recordEvent($transaction, 'failed', 'Generated payload failed validation.');

                return $transaction->fresh();
            }

            $transaction->update([
                'parsed_content' => $parsed,
                'status' => 'transformed',
            ]);

            $delivery = $this->deliveryGateway->deliver(
                $raw,
                $tenantId,
                $tradingPartnerId,
                $documentType,
                $options['delivery'] ?? [],
            );

            $transaction->update([
                'status' => ($delivery['success'] ?? false) ? 'completed' : 'failed',
                'error_message' => ($delivery['success'] ?? false) ? null : ($delivery['message'] ?? 'Delivery failed'),
                'processed_at' => now(),
            ]);

            $this->recordEvent(
                $transaction,
                $transaction->status,
                ($delivery['success'] ?? false) ? 'Delivered successfully.' : 'Delivery failed.'
            );

            return $transaction->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $mapped
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    private function buildTransactionPayload(
        string $standard,
        string $documentType,
        array $mapped,
        int $tenantId,
        int $tradingPartnerId,
    ): array {
        if ($standard === 'edifact') {
            return array_merge($mapped, [
                'document_type' => strtoupper($documentType),
                'message_reference' => $mapped['message_reference'] ?? '1',
                'po_number' => $mapped['po_number'] ?? null,
                'lines' => $mapped['lines'] ?? [],
            ]);
        }

        return array_merge($mapped, [
            'document_type' => $documentType,
            'control_number' => $this->controlNumberService->nextSt($tenantId, $tradingPartnerId),
            'header' => $mapped['header'] ?? [],
            'lines' => $mapped['lines'] ?? [],
        ]);
    }

    private function recordEvent(EdiTransaction $transaction, string $status, string $message): void
    {
        EdiTransactionEvent::query()->create([
            'edi_transaction_id' => $transaction->id,
            'event' => $status,
            'status' => $status,
            'level' => 'info',
            'message' => $message,
        ]);
    }
}
