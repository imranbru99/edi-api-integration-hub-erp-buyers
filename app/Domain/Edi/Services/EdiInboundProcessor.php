<?php

declare(strict_types=1);

namespace App\Domain\Edi\Services;

use App\Domain\Edi\Canonical\CanonicalInvoice;
use App\Domain\Edi\Canonical\CanonicalPurchaseOrder;
use App\Domain\Edi\Canonical\CanonicalShipment;
use App\Domain\Edi\Mapping\MappingEngine;
use App\Domain\Edi\Parsers\EdifactParser;
use App\Domain\Edi\Parsers\X12Parser;
use App\Models\EdiTransaction;
use App\Models\EdiTransactionEvent;
use App\Models\MappingProfile;
use Illuminate\Support\Facades\DB;

final class EdiInboundProcessor
{
    public const STATUS_RECEIVED = 'received';

    public const STATUS_VALIDATING = 'validating';

    public const STATUS_VALIDATED = 'validated';

    public const STATUS_MAPPING = 'mapping';

    public const STATUS_TRANSFORMED = 'transformed';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_QUARANTINED = 'quarantined';

    public function __construct(
        private readonly X12Parser $x12Parser,
        private readonly EdifactParser $edifactParser,
        private readonly EdiValidationService $validationService,
        private readonly IdempotencyService $idempotencyService,
        private readonly MappingEngine $mappingEngine,
        private readonly AcknowledgmentService $acknowledgmentService,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public function process(
        string $rawContent,
        int $tenantId,
        int $tradingPartnerId,
        ?string $documentTypeHint = null,
        array $context = [],
    ): EdiTransaction {
        return DB::transaction(function () use ($rawContent, $tenantId, $tradingPartnerId, $documentTypeHint, $context): EdiTransaction {
            $transaction = EdiTransaction::query()->create([
                'tenant_id' => $tenantId,
                'trading_partner_id' => $tradingPartnerId,
                'direction' => 'inbound',
                'standard' => $this->detectStandard($rawContent),
                'document_type' => $documentTypeHint,
                'status' => self::STATUS_RECEIVED,
                'raw_content' => $rawContent,
            ]);

            $this->recordEvent($transaction, self::STATUS_RECEIVED, 'Inbound EDI received.');

            try {
                $standard = (string) $transaction->standard;
                $parsed = $standard === 'edifact'
                    ? $this->edifactParser->parse($rawContent)
                    : $this->x12Parser->parse($rawContent);

                $documentType = $documentTypeHint
                    ?? $this->extractDocumentType($standard, $parsed);

                $transaction->update([
                    'document_type' => $documentType,
                    'parsed_content' => $parsed,
                    'control_number' => $this->extractControlNumber($standard, $parsed),
                    'status' => self::STATUS_VALIDATING,
                ]);
                $this->recordEvent($transaction, self::STATUS_VALIDATING, 'Structural validation started.');

                $errors = $this->validationService->validate($standard, $parsed);
                if ($errors !== []) {
                    $transaction->update([
                        'status' => self::STATUS_QUARANTINED,
                        'error_message' => implode(' ', $errors),
                    ]);
                    $this->recordEvent($transaction, self::STATUS_QUARANTINED, 'Validation failed.');

                    return $transaction->fresh();
                }

                $transaction->update(['status' => self::STATUS_VALIDATED]);
                $this->recordEvent($transaction, self::STATUS_VALIDATED, 'Validation passed.');

                $idempotencyKey = $this->idempotencyService->hash($rawContent, $tradingPartnerId, (string) $documentType);
                if ($this->idempotencyService->isDuplicate($idempotencyKey, $transaction->id)) {
                    $transaction->update([
                        'status' => self::STATUS_QUARANTINED,
                        'idempotency_key' => $idempotencyKey,
                        'error_message' => 'Duplicate inbound interchange detected.',
                    ]);
                    $this->recordEvent($transaction, self::STATUS_QUARANTINED, 'Duplicate detected.');

                    return $transaction->fresh();
                }

                $transaction->update(['idempotency_key' => $idempotencyKey]);

                $mapped = $parsed;
                $profile = MappingProfile::query()
                    ->where('tenant_id', $tenantId)
                    ->where('trading_partner_id', $tradingPartnerId)
                    ->where('direction', 'inbound')
                    ->where('document_type', $documentType)
                    ->with('rules')
                    ->first();

                if ($profile !== null) {
                    $transaction->update(['status' => self::STATUS_MAPPING]);
                    $this->recordEvent($transaction, self::STATUS_MAPPING, 'Applying mapping profile.');
                    $rules = $profile->rules->map(fn ($rule) => $rule->toMappingArray())->all();
                    $mapped = $this->mappingEngine->apply($parsed, $rules);
                    $transaction->update(['transformed_content' => $mapped]);
                }

                $canonical = $this->toCanonical($standard, (string) $documentType, $mapped);
                $transaction->update([
                    'canonical_content' => $canonical,
                    'status' => self::STATUS_TRANSFORMED,
                ]);
                $this->recordEvent($transaction, self::STATUS_TRANSFORMED, 'Canonical model built.');

                $ack = $this->acknowledgmentService->generateFunctionalAck(
                    $standard,
                    $parsed,
                    $tenantId,
                    $tradingPartnerId,
                    (int) $transaction->id,
                    $context['metadata'] ?? [],
                );

                $transaction->update([
                    'acknowledgment_id' => $ack['model']->id,
                    'status' => self::STATUS_COMPLETED,
                    'processed_at' => now(),
                ]);
                $this->recordEvent($transaction, self::STATUS_COMPLETED, 'Processing completed with functional ack.');

                return $transaction->fresh();
            } catch (\Throwable $exception) {
                $transaction->update([
                    'status' => self::STATUS_FAILED,
                    'error_message' => $exception->getMessage(),
                ]);
                $this->recordEvent($transaction, self::STATUS_FAILED, $exception->getMessage());

                return $transaction->fresh();
            }
        });
    }

    private function detectStandard(string $raw): string
    {
        $trimmed = ltrim($raw);

        if (str_starts_with($trimmed, 'ISA') || str_contains($trimmed, 'ST*')) {
            return 'x12';
        }

        if (str_starts_with($trimmed, 'UNA') || str_starts_with($trimmed, 'UNB')) {
            return 'edifact';
        }

        return 'x12';
    }

    /**
     * @param  array<string, mixed>  $parsed
     */
    private function extractDocumentType(string $standard, array $parsed): ?string
    {
        $tx = $parsed['transactions'][0] ?? null;
        if ($tx === null) {
            return null;
        }

        return $standard === 'edifact'
            ? ($tx['document_type'] ?? null)
            : ($tx['document_type'] ?? null);
    }

    /**
     * @param  array<string, mixed>  $parsed
     */
    private function extractControlNumber(string $standard, array $parsed): ?string
    {
        $tx = $parsed['transactions'][0] ?? null;
        if ($tx === null) {
            return null;
        }

        if ($standard === 'edifact') {
            return $tx['message_reference'] ?? null;
        }

        return $tx['control_number'] ?? null;
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @return array<string, mixed>
     */
    private function toCanonical(string $standard, string $documentType, array $parsed): array
    {
        $tx = $parsed['transactions'][0] ?? $parsed;

        return match (strtoupper($documentType)) {
            '850', 'ORDERS' => $this->canonicalPurchaseOrder($standard, $tx)->toArray(),
            '856', 'DESADV' => $this->canonicalShipment($standard, $tx)->toArray(),
            '810', 'INVOIC' => $this->canonicalInvoice($standard, $tx)->toArray(),
            default => ['document_type' => $documentType, 'parsed' => $tx],
        };
    }

    /**
     * @param  array<string, mixed>  $tx
     */
    private function canonicalPurchaseOrder(string $standard, array $tx): CanonicalPurchaseOrder
    {
        if ($standard === 'edifact') {
            $bgm = $tx['header']['bgm'][2]['components'][0] ?? '';

            $lines = [];
            foreach ($tx['lines'] ?? [] as $line) {
                $sku = $line['lin'][3]['components'][0] ?? '';
                $qty = $line['qty'][1]['components'][1] ?? '0';
                $lines[] = ['line_number' => $line['lin'][1]['components'][0] ?? '', 'sku' => $sku, 'quantity' => $qty];
            }

            return new CanonicalPurchaseOrder($bgm, null, '220', $lines);
        }

        $beg = $tx['header']['beg'] ?? [];
        $lines = [];
        foreach ($tx['lines'] ?? [] as $line) {
            $po1 = $line['po1'] ?? [];
            $lines[] = [
                'line_number' => $po1[1] ?? '',
                'quantity' => $po1[2] ?? '',
                'uom' => $po1[3] ?? '',
                'unit_price' => $po1[4] ?? '',
                'sku' => $po1[7] ?? '',
            ];
        }

        return new CanonicalPurchaseOrder(
            poNumber: (string) ($beg[3] ?? ''),
            orderDate: isset($beg[5]) ? (string) $beg[5] : null,
            purposeCode: isset($beg[1]) ? (string) $beg[1] : null,
            lines: $lines,
        );
    }

    /**
     * @param  array<string, mixed>  $tx
     */
    private function canonicalShipment(string $standard, array $tx): CanonicalShipment
    {
        if ($standard === 'edifact') {
            $bgm = $tx['header']['bgm'][2]['components'][0] ?? 'SHIP';
            $lines = [];
            foreach ($tx['lines'] ?? [] as $line) {
                $lines[] = ['sku' => $line['lin'][3]['components'][0] ?? ''];
            }

            return new CanonicalShipment($bgm, null, $lines);
        }

        $bsn = $tx['header']['bsn'] ?? [];
        $lines = [];
        foreach ($tx['lines'] ?? [] as $line) {
            $lin = $line['lin'] ?? [];
            $lines[] = [
                'sku' => $lin[3] ?? '',
                'quantity' => $line['sn1'][2] ?? '',
            ];
        }

        return new CanonicalShipment(
            shipmentId: (string) ($bsn[2] ?? ''),
            shipDate: isset($bsn[3]) ? (string) $bsn[3] : null,
            lines: $lines,
        );
    }

    /**
     * @param  array<string, mixed>  $tx
     */
    private function canonicalInvoice(string $standard, array $tx): CanonicalInvoice
    {
        if ($standard === 'edifact') {
            $bgm = $tx['header']['bgm'][2]['components'][0] ?? '';
            $total = $tx['header']['moa'][1]['components'][1] ?? null;

            return new CanonicalInvoice($bgm, null, null, $total !== null ? (string) $total : null);
        }

        $big = $tx['header']['big'] ?? [];
        $tds = $tx['header']['tds'] ?? [];

        return new CanonicalInvoice(
            invoiceNumber: (string) ($big[2] ?? ''),
            poNumber: isset($big[4]) ? (string) $big[4] : null,
            invoiceDate: isset($big[1]) ? (string) $big[1] : null,
            totalAmount: isset($tds[1]) ? (string) $tds[1] : null,
        );
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
