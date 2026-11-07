<?php

declare(strict_types=1);

namespace App\Domain\Edi\Generators;

final class EdifactGenerator
{
    private string $component = ':';

    private string $element = '+';

    private string $segment = "'";

    /**
     * @param  array<string, mixed>  $payload
     */
    public function generate(array $payload): string
    {
        $meta = $payload['metadata'] ?? [];
        $message = $payload['transaction'] ?? [];
        $documentType = strtoupper((string) ($message['document_type'] ?? 'ORDERS'));
        $messageRef = (string) ($message['message_reference'] ?? '1');
        $interchangeRef = (string) ($meta['interchange_control'] ?? '1');

        $bodySegments = $this->buildMessageSegments($documentType, $message);
        $unh = $this->segment('UNH', [
            $messageRef,
            $documentType.':D:96A:UN',
        ]);
        $unt = $this->segment('UNT', [
            (string) (count($bodySegments) + 2),
            $messageRef,
        ]);

        $unb = $this->segment('UNB', [
            'UNOC:3',
            ($meta['sender_id'] ?? 'SENDER').':14',
            ($meta['receiver_id'] ?? 'RECEIVER').':14',
            date('ymd:Hi'),
            $interchangeRef,
        ]);
        $unz = $this->segment('UNZ', ['1', $interchangeRef]);

        $parts = [
            'UNA'.$this->component.$this->element.'.? '.$this->segment,
            $this->formatSegment($unb),
            $this->formatSegment($unh),
            ...array_map(fn (array $s) => $this->formatSegment($s), $bodySegments),
            $this->formatSegment($unt),
            $this->formatSegment($unz),
        ];

        return implode('', $parts);
    }

    /**
     * @param  array<string, mixed>  $message
     * @return list<array{tag: string, elements: list<string>}>
     */
    private function buildMessageSegments(string $documentType, array $message): array
    {
        return match ($documentType) {
            'ORDERS' => $this->buildOrders($message),
            'ORDRSP' => $this->buildOrdrsp($message),
            'DESADV' => $this->buildDesadv($message),
            'INVOIC' => $this->buildInvoic($message),
            'CONTRL' => $this->buildContrl($message),
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>  $message
     * @return list<array{tag: string, elements: list<string>}>
     */
    private function buildOrders(array $message): array
    {
        $segments = [];
        $segments[] = $this->segment('BGM', [
            '220',
            $message['po_number'] ?? ($message['header']['bgm'][1]['components'][1] ?? 'PO-1'),
            '9',
        ]);
        $segments[] = $this->segment('DTM', ['137:'.date('Ymd').':102']);

        foreach ($message['lines'] ?? [] as $index => $line) {
            $lin = $line['lin'] ?? $line;
            $item = is_array($lin) && isset($lin[2]['components'][1])
                ? $lin[2]['components'][1]
                : ($line['sku'] ?? 'ITEM-'.($index + 1));
            $segments[] = $this->segment('LIN', [(string) ($index + 1), '', $item.':VP']);
            $qty = $line['qty'] ?? $line['quantity'] ?? '1';
            $qtyValue = is_array($qty) ? ($qty[0]['components'][1] ?? '1') : (string) $qty;
            $segments[] = $this->segment('QTY', ['21:'.$qtyValue]);
        }

        $count = count($message['lines'] ?? []);
        $segments[] = $this->segment('UNS', ['S']);
        $segments[] = $this->segment('CNT', ['2:'.max(1, $count)]);

        return $segments;
    }

    /**
     * @param  array<string, mixed>  $message
     * @return list<array{tag: string, elements: list<string>}>
     */
    private function buildOrdrsp(array $message): array
    {
        return [
            $this->segment('BGM', [
                '231',
                $message['po_number'] ?? 'PO-1',
                '9',
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $message
     * @return list<array{tag: string, elements: list<string>}>
     */
    private function buildDesadv(array $message): array
    {
        $segments = [
            $this->segment('BGM', [
                '351',
                $message['shipment_id'] ?? 'SHIP-1',
                '9',
            ]),
        ];

        foreach ($message['lines'] ?? [] as $index => $line) {
            $sku = $line['sku'] ?? 'ITEM-'.($index + 1);
            $segments[] = $this->segment('LIN', [(string) ($index + 1), '', $sku.':VP']);
        }

        return $segments;
    }

    /**
     * @param  array<string, mixed>  $message
     * @return list<array{tag: string, elements: list<string>}>
     */
    private function buildInvoic(array $message): array
    {
        $segments = [
            $this->segment('BGM', [
                '380',
                $message['invoice_number'] ?? 'INV-1',
                '9',
            ]),
        ];

        foreach ($message['lines'] ?? [] as $index => $line) {
            $sku = $line['sku'] ?? 'ITEM-'.($index + 1);
            $segments[] = $this->segment('LIN', [(string) ($index + 1), '', $sku.':VP']);
        }

        $segments[] = $this->segment('MOA', [
            '79:'.($message['total'] ?? '0'),
        ]);

        return $segments;
    }

    /**
     * @param  array<string, mixed>  $message
     * @return list<array{tag: string, elements: list<string>}>
     */
    private function buildContrl(array $message): array
    {
        $segments = [];
        $uci = $message['header']['uci'] ?? [];
        $segments[] = $this->segment('UCI', [
            $message['interchange_ref'] ?? '1',
            ($message['metadata']['sender_id'] ?? 'SENDER').':14',
            ($message['metadata']['receiver_id'] ?? 'RECEIVER').':14',
            '7',
        ]);

        foreach ($message['acknowledgments'] ?? [] as $ack) {
            $segments[] = $this->segment('UCM', [
                $ack['message_ref'] ?? '1',
                $ack['document_type'] ?? 'ORDERS',
                '7',
            ]);
        }

        if ($uci !== []) {
            $segments[0] = $this->segment('UCI', array_map(
                fn ($el) => is_array($el) ? implode($this->component, $el['components'] ?? []) : (string) $el,
                $uci
            ));
        }

        return $segments;
    }

    /**
     * @param  list<string>  $elements
     * @return array{tag: string, elements: list<string>}
     */
    private function segment(string $tag, array $elements): array
    {
        return ['tag' => $tag, 'elements' => $elements];
    }

    /**
     * @param  array{tag: string, elements: list<string>}  $segment
     */
    private function formatSegment(array $segment): string
    {
        $parts = [$segment['tag'], ...$segment['elements']];

        return implode($this->element, $parts).$this->segment;
    }
}
