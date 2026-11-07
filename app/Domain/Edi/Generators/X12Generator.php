<?php

declare(strict_types=1);

namespace App\Domain\Edi\Generators;

final class X12Generator
{
    private string $elementSeparator = '*';

    private string $segmentTerminator = '~';

    /**
     * @param  array<string, mixed>  $payload
     */
    public function generate(array $payload): string
    {
        $isa = $payload['envelope']['isa'] ?? $this->defaultIsa($payload);
        $gs = $payload['envelope']['gs'] ?? $this->defaultGs($payload);
        $iea = $payload['envelope']['iea'] ?? null;
        $transaction = $payload['transaction'] ?? [];

        $documentType = (string) ($transaction['document_type'] ?? '850');
        $stControl = (string) ($transaction['control_number'] ?? '0001');

        $bodySegments = $this->buildDocumentSegments($documentType, $transaction);
        $st = $this->segment('ST', [$documentType, $stControl]);
        $segmentCount = count($bodySegments) + 2;
        $se = $this->segment('SE', [(string) $segmentCount, $stControl]);

        $transactionSegments = array_merge([$st], $bodySegments, [$se]);
        $ge = $payload['envelope']['ge'] ?? $this->segment('GE', ['1', $gs['elements'][6] ?? '1']);
        $iea = $iea ?? $this->segment('IEA', ['1', $isa['elements'][13] ?? '000000001']);

        $parts = [
            $this->formatSegment($isa),
            $this->formatSegment($gs),
            ...array_map(fn (array $s) => $this->formatSegment($s), $transactionSegments),
            $this->formatSegment($ge),
            $this->formatSegment($iea),
        ];

        return implode('', $parts);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{elements: list<string>}
     */
    private function defaultIsa(array $payload): array
    {
        $meta = $payload['metadata'] ?? [];

        return $this->segment('ISA', [
            '00', str_pad('', 10),
            '00', str_pad('', 10),
            'ZZ', str_pad($meta['sender_id'] ?? 'SENDER', 15),
            'ZZ', str_pad($meta['receiver_id'] ?? 'RECEIVER', 15),
            date('ymd'), date('Hi'),
            'U', '00401',
            str_pad((string) ($meta['isa_control'] ?? '1'), 9, '0', STR_PAD_LEFT),
            '0', 'P', '>',
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{elements: list<string>}
     */
    private function defaultGs(array $payload): array
    {
        $meta = $payload['metadata'] ?? [];
        $documentType = $payload['transaction']['document_type'] ?? '850';
        $functionalId = match ($documentType) {
            '997' => 'FA',
            '810' => 'IN',
            '856' => 'SH',
            default => 'PO',
        };

        return $this->segment('GS', [
            $functionalId,
            $meta['sender_id'] ?? 'SENDER',
            $meta['receiver_id'] ?? 'RECEIVER',
            date('Ymd'), date('Hi'),
            (string) ($meta['gs_control'] ?? '1'),
            'X', '004010',
        ]);
    }

    /**
     * @param  array<string, mixed>  $transaction
     * @return list<array{elements: list<string>}>
     */
    private function buildDocumentSegments(string $documentType, array $transaction): array
    {
        return match ($documentType) {
            '850' => $this->build850($transaction),
            '855' => $this->build855($transaction),
            '856' => $this->build856($transaction),
            '810' => $this->build810($transaction),
            '997' => $this->build997($transaction),
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>  $transaction
     * @return list<array{elements: list<string>}>
     */
    private function build850(array $transaction): array
    {
        $segments = [];
        $beg = $transaction['header']['beg'] ?? [];
        $segments[] = $this->segment('BEG', [
            $beg[1] ?? '00',
            $beg[2] ?? 'SA',
            $beg[3] ?? ($transaction['po_number'] ?? 'PO-UNKNOWN'),
            $beg[4] ?? '',
            $beg[5] ?? date('Ymd'),
        ]);

        foreach ($transaction['lines'] ?? [] as $line) {
            $po1 = $line['po1'] ?? $line;
            $segments[] = $this->segment('PO1', [
                $po1[1] ?? '1',
                $po1[2] ?? '1',
                $po1[3] ?? 'EA',
                $po1[4] ?? '0',
                '',
                $po1[6] ?? 'VP',
                $po1[7] ?? 'ITEM',
            ]);
        }

        $lineCount = count($transaction['lines'] ?? []);
        $segments[] = $this->segment('CTT', [(string) max(1, $lineCount)]);

        return $segments;
    }

    /**
     * @param  array<string, mixed>  $transaction
     * @return list<array{elements: list<string>}>
     */
    private function build855(array $transaction): array
    {
        $bak = $transaction['header']['bak'] ?? [];

        return [
            $this->segment('BAK', [
                $bak[1] ?? '00',
                $bak[2] ?? 'AC',
                $bak[3] ?? ($transaction['po_number'] ?? ''),
                $bak[4] ?? date('Ymd'),
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $transaction
     * @return list<array{elements: list<string>}>
     */
    private function build856(array $transaction): array
    {
        $segments = [];
        $bsn = $transaction['header']['bsn'] ?? [];
        $segments[] = $this->segment('BSN', [
            $bsn[1] ?? '00',
            $bsn[2] ?? ($transaction['shipment_id'] ?? 'SHIP-1'),
            $bsn[3] ?? date('Ymd'),
        ]);

        $hl = 1;
        foreach ($transaction['lines'] ?? [] as $line) {
            $segments[] = $this->segment('HL', [(string) $hl, '', '1']);
            $lin = $line['lin'] ?? $line['po1'] ?? [];
            $segments[] = $this->segment('LIN', [
                $lin[1] ?? (string) $hl,
                '', '',
                $lin[7] ?? $lin[3] ?? 'ITEM',
                $lin[6] ?? 'VP',
            ]);
            $sn1 = $line['sn1'] ?? [];
            $segments[] = $this->segment('SN1', [
                '',
                $sn1[2] ?? $lin[2] ?? '1',
                $sn1[3] ?? $lin[3] ?? 'EA',
            ]);
            $hl++;
        }

        return $segments;
    }

    /**
     * @param  array<string, mixed>  $transaction
     * @return list<array{elements: list<string>}>
     */
    private function build810(array $transaction): array
    {
        $segments = [];
        $big = $transaction['header']['big'] ?? [];
        $segments[] = $this->segment('BIG', [
            $big[1] ?? date('Ymd'),
            $big[2] ?? ($transaction['invoice_number'] ?? 'INV-1'),
            $big[3] ?? '',
            $big[4] ?? ($transaction['po_number'] ?? ''),
        ]);

        foreach ($transaction['lines'] ?? [] as $line) {
            $it1 = $line['it1'] ?? $line;
            $segments[] = $this->segment('IT1', [
                $it1[1] ?? '1',
                $it1[2] ?? '1',
                $it1[3] ?? 'EA',
                $it1[4] ?? '0',
                '',
                $it1[6] ?? 'VP',
                $it1[7] ?? 'ITEM',
            ]);
        }

        $total = $transaction['header']['tds'][1] ?? $transaction['total'] ?? '0';
        $segments[] = $this->segment('TDS', [(string) $total]);

        return $segments;
    }

    /**
     * @param  array<string, mixed>  $transaction
     * @return list<array{elements: list<string>}>
     */
    private function build997(array $transaction): array
    {
        $segments = [];
        $ak1 = $transaction['header']['ak1'] ?? [];
        $segments[] = $this->segment('AK1', [
            $ak1[1] ?? 'PO',
            $ak1[2] ?? '1',
        ]);

        foreach ($transaction['acknowledgments'] ?? [] as $ack) {
            $ak2 = $ack['ak2'] ?? $ack;
            $segments[] = $this->segment('AK2', [
                $ak2[1] ?? '850',
                $ak2[2] ?? '0001',
            ]);
        }

        $ak5 = $transaction['header']['ak5'] ?? [];
        $segments[] = $this->segment('AK5', [$ak5[1] ?? 'A']);
        $ak9 = $transaction['header']['ak9'] ?? [];
        $segments[] = $this->segment('AK9', [
            $ak9[1] ?? 'A',
            $ak9[2] ?? '1',
            $ak9[3] ?? '1',
            $ak9[4] ?? '1',
        ]);

        return $segments;
    }

    /**
     * @param  list<string>  $elements
     * @return array{elements: list<string>}
     */
    private function segment(string $id, array $elements): array
    {
        return ['elements' => array_merge([$id], $elements)];
    }

    /**
     * @param  array{elements: list<string>}|array<string, mixed>  $segment
     */
    private function formatSegment(array $segment): string
    {
        $elements = $segment['elements'] ?? [];
        if (isset($segment['id'])) {
            $elements = array_merge([$segment['id']], $segment['elements'] ?? []);
        }

        return implode($this->elementSeparator, $elements).$this->segmentTerminator;
    }
}
