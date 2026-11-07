<?php

declare(strict_types=1);

namespace App\Domain\Edi\Parsers;

final class X12Parser
{
    private const SUPPORTED_DOCUMENTS = ['850', '855', '856', '810', '997'];

    /**
     * @return array<string, mixed>
     */
    public function parse(string $raw): array
    {
        $raw = $this->normalize($raw);
        $segmentStrings = $this->splitSegments($raw);

        $result = [
            'standard' => 'x12',
            'envelope' => [
                'isa' => null,
                'iea' => null,
                'functional_groups' => [],
            ],
            'transactions' => [],
        ];

        if ($segmentStrings === []) {
            return $result;
        }

        $index = 0;
        $total = count($segmentStrings);

        if ($this->segmentId($segmentStrings[$index]) === 'ISA') {
            $result['envelope']['isa'] = $this->parseSegment($segmentStrings[$index++]);
        }

        while ($index < $total) {
            $id = $this->segmentId($segmentStrings[$index]);
            if ($id === 'IEA') {
                $result['envelope']['iea'] = $this->parseSegment($segmentStrings[$index++]);

                break;
            }

            if ($id !== 'GS') {
                $index++;

                continue;
            }

            $group = [
                'gs' => $this->parseSegment($segmentStrings[$index++]),
                'ge' => null,
                'transactions' => [],
            ];

            while ($index < $total) {
                $innerId = $this->segmentId($segmentStrings[$index]);
                if ($innerId === 'GE') {
                    $group['ge'] = $this->parseSegment($segmentStrings[$index++]);

                    break;
                }

                if ($innerId !== 'ST') {
                    $index++;

                    continue;
                }

                $transaction = $this->parseTransaction($segmentStrings, $index);
                $index = $transaction['_next_index'];
                unset($transaction['_next_index']);
                $group['transactions'][] = $transaction;
                $result['transactions'][] = $transaction;
            }

            $result['envelope']['functional_groups'][] = $group;
        }

        return $result;
    }

    /**
     * @param  list<string>  $segments
     * @return array<string, mixed>
     */
    private function parseTransaction(array $segments, int $startIndex): array
    {
        $st = $this->parseSegment($segments[$startIndex]);
        $documentType = $st['elements'][1] ?? '';
        $controlNumber = $st['elements'][2] ?? '';

        $bodySegments = [];
        $index = $startIndex + 1;
        $total = count($segments);

        while ($index < $total) {
            $id = $this->segmentId($segments[$index]);
            if ($id === 'SE') {
                $se = $this->parseSegment($segments[$index]);
                $index++;

                $parsed = [
                    'st' => $st,
                    'se' => $se,
                    'document_type' => $documentType,
                    'control_number' => $controlNumber,
                    'segments' => $bodySegments,
                    '_next_index' => $index,
                ];

                $parsed = array_merge($parsed, $this->parseDocumentBody($documentType, $bodySegments));

                return $parsed;
            }

            $bodySegments[] = $this->parseSegment($segments[$index]);
            $index++;
        }

        return [
            'st' => $st,
            'se' => null,
            'document_type' => $documentType,
            'control_number' => $controlNumber,
            'segments' => $bodySegments,
            '_next_index' => $index,
        ];
    }

    /**
     * @param  list<array{id: string, elements: list<string>, raw: string}>  $segments
     * @return array<string, mixed>
     */
    private function parseDocumentBody(string $documentType, array $segments): array
    {
        if (! in_array($documentType, self::SUPPORTED_DOCUMENTS, true)) {
            return ['header' => [], 'lines' => [], 'hierarchy' => [], 'acknowledgments' => []];
        }

        return match ($documentType) {
            '850' => $this->parse850($segments),
            '855' => $this->parse855($segments),
            '856' => $this->parse856($segments),
            '810' => $this->parse810($segments),
            '997' => $this->parse997($segments),
            default => ['header' => [], 'lines' => [], 'hierarchy' => [], 'acknowledgments' => []],
        };
    }

    /**
     * @param  list<array{id: string, elements: list<string>, raw: string}>  $segments
     * @return array<string, mixed>
     */
    private function parse850(array $segments): array
    {
        $header = [];
        $lines = [];
        $ctt = null;

        foreach ($segments as $segment) {
            match ($segment['id']) {
                'BEG' => $header['beg'] = $this->indexedElements($segment['elements']),
                'PO1' => $lines[] = ['po1' => $this->indexedElements($segment['elements'])],
                'CTT' => $ctt = $this->indexedElements($segment['elements']),
                default => null,
            };
        }

        return [
            'header' => array_merge($header, ['ctt' => $ctt]),
            'lines' => $lines,
            'hierarchy' => [],
            'acknowledgments' => [],
        ];
    }

    /**
     * @param  list<array{id: string, elements: list<string>, raw: string}>  $segments
     * @return array<string, mixed>
     */
    private function parse855(array $segments): array
    {
        $header = [];

        foreach ($segments as $segment) {
            if ($segment['id'] === 'BAK') {
                $header['bak'] = $this->indexedElements($segment['elements']);
            }
        }

        return [
            'header' => $header,
            'lines' => [],
            'hierarchy' => [],
            'acknowledgments' => [],
        ];
    }

    /**
     * @param  list<array{id: string, elements: list<string>, raw: string}>  $segments
     * @return array<string, mixed>
     */
    private function parse856(array $segments): array
    {
        $header = [];
        $hierarchy = [];
        $lines = [];
        $currentHl = null;

        foreach ($segments as $segment) {
            match ($segment['id']) {
                'BSN' => $header['bsn'] = $this->indexedElements($segment['elements']),
                'HL' => $currentHl = [
                    'hl' => $this->indexedElements($segment['elements']),
                    'lin' => null,
                    'sn1' => null,
                ],
                'LIN' => $this->attachToHl($currentHl, $hierarchy, 'lin', $this->indexedElements($segment['elements'])),
                'SN1' => $this->attachToHl($currentHl, $hierarchy, 'sn1', $this->indexedElements($segment['elements'])),
                default => null,
            };
        }

        foreach ($hierarchy as $node) {
            if ($node['lin'] !== null) {
                $lines[] = $node;
            }
        }

        return [
            'header' => $header,
            'lines' => $lines,
            'hierarchy' => $hierarchy,
            'acknowledgments' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $currentHl
     * @param  list<array<string, mixed>>  $hierarchy
     * @param  array<int, string>  $elements
     */
    private function attachToHl(?array &$currentHl, array &$hierarchy, string $key, array $elements): void
    {
        if ($currentHl === null) {
            return;
        }

        $currentHl[$key] = $elements;
        if ($key === 'lin') {
            $hierarchy[] = $currentHl;
        } elseif ($key === 'sn1' && $hierarchy !== []) {
            $last = array_key_last($hierarchy);
            $hierarchy[$last]['sn1'] = $elements;
            $currentHl = $hierarchy[$last];
        }
    }

    /**
     * @param  list<array{id: string, elements: list<string>, raw: string}>  $segments
     * @return array<string, mixed>
     */
    private function parse810(array $segments): array
    {
        $header = [];
        $lines = [];
        $tds = null;

        foreach ($segments as $segment) {
            match ($segment['id']) {
                'BIG' => $header['big'] = $this->indexedElements($segment['elements']),
                'IT1' => $lines[] = ['it1' => $this->indexedElements($segment['elements'])],
                'TDS' => $tds = $this->indexedElements($segment['elements']),
                default => null,
            };
        }

        if ($tds !== null) {
            $header['tds'] = $tds;
        }

        return [
            'header' => $header,
            'lines' => $lines,
            'hierarchy' => [],
            'acknowledgments' => [],
        ];
    }

    /**
     * @param  list<array{id: string, elements: list<string>, raw: string}>  $segments
     * @return array<string, mixed>
     */
    private function parse997(array $segments): array
    {
        $header = [];
        $acknowledgments = [];

        foreach ($segments as $segment) {
            match ($segment['id']) {
                'AK1' => $header['ak1'] = $this->indexedElements($segment['elements']),
                'AK2' => $acknowledgments[] = ['ak2' => $this->indexedElements($segment['elements'])],
                'AK5' => $header['ak5'] = $this->indexedElements($segment['elements']),
                'AK9' => $header['ak9'] = $this->indexedElements($segment['elements']),
                default => null,
            };
        }

        return [
            'header' => $header,
            'lines' => [],
            'hierarchy' => [],
            'acknowledgments' => $acknowledgments,
        ];
    }

    /**
     * @param  list<string>  $elements
     * @return array<int, string>
     */
    private function indexedElements(array $elements): array
    {
        $indexed = [];
        foreach ($elements as $position => $value) {
            if ($position === 0) {
                continue;
            }
            $indexed[$position] = $value;
        }

        return $indexed;
    }

    private function normalize(string $raw): string
    {
        $raw = str_replace(["\r\n", "\r", "\n"], '', trim($raw));

        return $raw;
    }

    /**
     * @return list<string>
     */
    private function splitSegments(string $raw): array
    {
        $parts = preg_split('/~+/', $raw) ?: [];
        $segments = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '') {
                $segments[] = $part;
            }
        }

        return $segments;
    }

    /**
     * @return array{id: string, elements: list<string>, raw: string}
     */
    private function parseSegment(string $segment): array
    {
        $elements = explode('*', $segment);
        $id = $elements[0] ?? '';

        return [
            'id' => $id,
            'elements' => $elements,
            'raw' => $segment.'~',
        ];
    }

    private function segmentId(string $segment): string
    {
        $pos = strpos($segment, '*');

        return $pos === false ? $segment : substr($segment, 0, $pos);
    }
}
