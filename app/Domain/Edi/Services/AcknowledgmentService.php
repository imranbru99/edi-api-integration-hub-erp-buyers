<?php

declare(strict_types=1);

namespace App\Domain\Edi\Services;

use App\Domain\Edi\Generators\EdifactGenerator;
use App\Domain\Edi\Generators\X12Generator;
use App\Models\EdiAcknowledgment;

final class AcknowledgmentService
{
    public function __construct(
        private readonly X12Generator $x12Generator,
        private readonly EdifactGenerator $edifactGenerator,
        private readonly ControlNumberService $controlNumbers,
    ) {}

    /**
     * @param  array<string, mixed>  $parsed
     * @param  array<string, mixed>  $metadata
     * @return array{standard: string, document_type: string, payload: string, model: EdiAcknowledgment}
     */
    public function generateFunctionalAck(
        string $standard,
        array $parsed,
        int $tenantId,
        int $tradingPartnerId,
        int $ediTransactionId,
        array $metadata = [],
    ): array {
        if ($standard === 'x12') {
            return $this->generate997($parsed, $tenantId, $tradingPartnerId, $ediTransactionId, $metadata);
        }

        return $this->generateContrl($parsed, $tenantId, $tradingPartnerId, $ediTransactionId, $metadata);
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @param  array<string, mixed>  $metadata
     * @return array{standard: string, document_type: string, payload: string, model: EdiAcknowledgment}
     */
    private function generate997(
        array $parsed,
        int $tenantId,
        int $tradingPartnerId,
        int $ediTransactionId,
        array $metadata,
    ): array {
        $inbound = $parsed['transactions'][0] ?? [];
        $gsControl = $parsed['envelope']['functional_groups'][0]['gs']['elements'][6] ?? '1';

        $transaction = [
            'document_type' => '997',
            'control_number' => $this->controlNumbers->nextSt($tenantId, $tradingPartnerId),
            'header' => [
                'ak1' => [1 => 'PO', 2 => $gsControl],
                'ak5' => [1 => 'A'],
                'ak9' => [1 => 'A', 2 => '1', 3 => '1', 4 => '1'],
            ],
            'acknowledgments' => [
                [
                    'ak2' => [
                        1 => $inbound['document_type'] ?? '850',
                        2 => $inbound['control_number'] ?? '0001',
                    ],
                ],
            ],
        ];

        $payload = $this->x12Generator->generate([
            'metadata' => array_merge($metadata, [
                'isa_control' => $this->controlNumbers->nextIsa($tenantId, $tradingPartnerId),
                'gs_control' => $this->controlNumbers->nextGs($tenantId, $tradingPartnerId),
            ]),
            'transaction' => $transaction,
        ]);

        $model = EdiAcknowledgment::query()->create([
            'edi_transaction_id' => $ediTransactionId,
            'ack_type' => 'functional',
            'standard' => 'x12',
            'document_type' => '997',
            'payload' => $payload,
            'status' => 'generated',
        ]);

        return [
            'standard' => 'x12',
            'document_type' => '997',
            'payload' => $payload,
            'model' => $model,
        ];
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @param  array<string, mixed>  $metadata
     * @return array{standard: string, document_type: string, payload: string, model: EdiAcknowledgment}
     */
    private function generateContrl(
        array $parsed,
        int $tenantId,
        int $tradingPartnerId,
        int $ediTransactionId,
        array $metadata,
    ): array {
        $inbound = $parsed['transactions'][0] ?? [];
        $interchangeRef = $parsed['envelope']['unb']['elements'][4]['components'][0] ?? '1';

        $transaction = [
            'document_type' => 'CONTRL',
            'message_reference' => '1',
            'interchange_ref' => $interchangeRef,
            'metadata' => $metadata,
            'acknowledgments' => [
                [
                    'message_ref' => $inbound['message_reference'] ?? '1',
                    'document_type' => $inbound['document_type'] ?? 'ORDERS',
                ],
            ],
        ];

        $payload = $this->edifactGenerator->generate([
            'metadata' => array_merge($metadata, [
                'interchange_control' => $this->controlNumbers->nextInterchange($tenantId, $tradingPartnerId),
            ]),
            'transaction' => $transaction,
        ]);

        $model = EdiAcknowledgment::query()->create([
            'edi_transaction_id' => $ediTransactionId,
            'ack_type' => 'functional',
            'standard' => 'edifact',
            'document_type' => 'CONTRL',
            'payload' => $payload,
            'status' => 'generated',
        ]);

        return [
            'standard' => 'edifact',
            'document_type' => 'CONTRL',
            'payload' => $payload,
            'model' => $model,
        ];
    }
}
