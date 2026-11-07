<?php

declare(strict_types=1);

namespace App\Domain\Edi\Parsers;

final class EdifactParser
{
    private const SUPPORTED_MESSAGES = ['ORDERS', 'ORDRSP', 'DESADV', 'INVOIC', 'CONTRL'];

    /**
     * @return array<string, mixed>
     */
    public function parse(string $raw): array
    {
        $raw = $this->normalize($raw);
        $delimiters = $this->resolveDelimiters($raw);
        $segmentStrings = $this->splitSegments($raw, $delimiters['segment']);

        $result = [
            'standard' => 'edifact',
            'delimiters' => $delimiters,
            'envelope' => [
                'una' => null,
                'unb' => null,
                'unz' => null,
                'messages' => [],
            ],
            'transactions' => [],
        ];

        $index = 0;
        $total = count($segmentStrings);

        if ($total > 0 && $this->segmentTag($segmentStrings[$index]) === 'UNA') {
            $result['envelope']['una'] = $this->parseSegment($segmentStrings[$index++], $delimiters);
        }

        if ($index < $total && $this->segmentTag($segmentStrings[$index]) === 'UNB') {
            $result['envelope']['unb'] = $this->parseSegment($segmentStrings[$index++], $delimiters);
        }

        while ($index < $total) {
            $tag = $this->segmentTag($segmentStrings[$index]);
            if ($tag === 'UNZ') {
                $result['envelope']['unz'] = $this->parseSegment($segmentStrings[$index++], $delimiters);

                break;
            }

            if ($tag !== 'UNH') {
                $index++;

                continue;
            }

            $message = $this->parseMessage($segmentStrings, $index, $delimiters);
            $index = $message['_next_index'];
            unset($message['_next_index']);
            $result['envelope']['messages'][] = $message;
            $result['transactions'][] = $message;
        }

        return $result;
    }

    /**
     * @param  list<string>  $segments
     * @return array<string, mixed>
     */
    private function parseMessage(array $segments, int $startIndex, array $delimiters): array
    {
        $unh = $this->parseSegment($segments[$startIndex], $delimiters);
        $messageRef = $unh['elements'][0]['components'][0] ?? '';
        $messageType = $this->messageTypeFromUnh($unh);

        $bodySegments = [];
        $index = $startIndex + 1;
        $total = count($segments);

        while ($index < $total) {
            $tag = $this->segmentTag($segments[$index]);
            if ($tag === 'UNT') {
                $unt = $this->parseSegment($segments[$index], $delimiters);
                $index++;

                $parsed = [
                    'unh' => $unh,
                    'unt' => $unt,
                    'message_reference' => $messageRef,
                    'document_type' => $messageType,
                    'segments' => $bodySegments,
                    '_next_index' => $index,
                ];

                $parsed = array_merge($parsed, $this->parseMessageBody($messageType, $bodySegments));

                return $parsed;
            }

            $bodySegments[] = $this->parseSegment($segments[$index], $delimiters);
            $index++;
        }

        return [
            'unh' => $unh,
            'unt' => null,
            'message_reference' => $messageRef,
            'document_type' => $messageType,
            'segments' => $bodySegments,
            '_next_index' => $index,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $segments
     * @return array<string, mixed>
     */
    private function parseMessageBody(string $messageType, array $segments): array
    {
        if (! in_array($messageType, self::SUPPORTED_MESSAGES, true)) {
            return ['header' => [], 'lines' => [], 'acknowledgments' => []];
        }

        return match ($messageType) {
            'ORDERS' => $this->parseOrders($segments),
            'ORDRSP' => $this->parseOrdrsp($segments),
            'DESADV' => $this->parseDesadv($segments),
            'INVOIC' => $this->parseInvoic($segments),
            'CONTRL' => $this->parseContrl($segments),
            default => ['header' => [], 'lines' => [], 'acknowledgments' => []],
        };
    }

    /**
     * @param  list<array<string, mixed>>  $segments
     * @return array<string, mixed>
     */
    private function parseOrders(array $segments): array
    {
        $header = [];
        $lines = [];
        $currentLine = null;

        foreach ($segments as $segment) {
            match ($segment['tag']) {
                'BGM' => $header['bgm'] = $segment['elements'],
                'DTM' => $header['dtm'][] = $segment['elements'],
                'LIN' => $currentLine = ['lin' => $segment['elements'], 'qty' => null],
                'QTY' => $this->attachQty($currentLine, $lines, $segment['elements']),
                'CNT' => $header['cnt'] = $segment['elements'],
                default => null,
            };
        }

        return ['header' => $header, 'lines' => $lines, 'acknowledgments' => []];
    }

    /**
     * @param  array<string, mixed>|null  $currentLine
     * @param  list<array<string, mixed>>  $lines
     * @param  list<array<string, mixed>>  $qtyElements
     */
    private function attachQty(?array &$currentLine, array &$lines, array $qtyElements): void
    {
        if ($currentLine === null) {
            return;
        }

        $currentLine['qty'] = $qtyElements;
        $lines[] = $currentLine;
        $currentLine = null;
    }

    /**
     * @param  list<array<string, mixed>>  $segments
     * @return array<string, mixed>
     */
    private function parseOrdrsp(array $segments): array
    {
        $header = [];

        foreach ($segments as $segment) {
            if ($segment['tag'] === 'BGM') {
                $header['bgm'] = $segment['elements'];
            }
        }

        return ['header' => $header, 'lines' => [], 'acknowledgments' => []];
    }

    /**
     * @param  list<array<string, mixed>>  $segments
     * @return array<string, mixed>
     */
    private function parseDesadv(array $segments): array
    {
        $header = [];
        $lines = [];

        foreach ($segments as $segment) {
            match ($segment['tag']) {
                'BGM' => $header['bgm'] = $segment['elements'],
                'LIN' => $lines[] = ['lin' => $segment['elements']],
                default => null,
            };
        }

        return ['header' => $header, 'lines' => $lines, 'acknowledgments' => []];
    }

    /**
     * @param  list<array<string, mixed>>  $segments
     * @return array<string, mixed>
     */
    private function parseInvoic(array $segments): array
    {
        $header = [];
        $lines = [];

        foreach ($segments as $segment) {
            match ($segment['tag']) {
                'BGM' => $header['bgm'] = $segment['elements'],
                'LIN' => $lines[] = ['lin' => $segment['elements']],
                'MOA' => $header['moa'] = $segment['elements'],
                default => null,
            };
        }

        return ['header' => $header, 'lines' => $lines, 'acknowledgments' => []];
    }

    /**
     * @param  list<array<string, mixed>>  $segments
     * @return array<string, mixed>
     */
    private function parseContrl(array $segments): array
    {
        $header = [];
        $acknowledgments = [];

        foreach ($segments as $segment) {
            match ($segment['tag']) {
                'UCI' => $header['uci'] = $segment['elements'],
                'UCM' => $acknowledgments[] = ['ucm' => $segment['elements']],
                default => null,
            };
        }

        return ['header' => $header, 'lines' => [], 'acknowledgments' => $acknowledgments];
    }

    /**
     * @return array{component: string, element: string, decimal: string, release: string, segment: string}
     */
    private function resolveDelimiters(string $raw): array
    {
        $defaults = [
            'component' => ':',
            'element' => '+',
            'decimal' => '.',
            'release' => '?',
            'segment' => "'",
        ];

        if (str_starts_with($raw, 'UNA')) {
            return [
                'component' => $raw[3] ?? ':',
                'element' => $raw[4] ?? '+',
                'decimal' => $raw[5] ?? '.',
                'release' => $raw[6] ?? '?',
                'segment' => $raw[8] ?? "'",
            ];
        }

        return $defaults;
    }

    private function normalize(string $raw): string
    {
        return str_replace(["\r\n", "\r", "\n"], '', trim($raw));
    }

    /**
     * @return list<string>
     */
    private function splitSegments(string $raw, string $segmentTerminator): array
    {
        $pattern = '#'.preg_quote($segmentTerminator, '#').'+#';
        $parts = preg_split($pattern, $raw) ?: [];
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
     * @param  array{component: string, element: string, decimal: string, release: string, segment: string}  $delimiters
     * @return array<string, mixed>
     */
    private function parseSegment(string $segment, array $delimiters): array
    {
        $release = $delimiters['release'];
        $elementSep = $delimiters['element'];
        $componentSep = $delimiters['component'];

        $unescaped = $this->unescapeSegment($segment, $release);
        $elementStrings = explode($elementSep, $unescaped);
        $tag = $elementStrings[0] ?? '';

        $elements = [];
        foreach ($elementStrings as $elementString) {
            $components = $elementString === ''
                ? []
                : explode($componentSep, $elementString);
            $elements[] = [
                'raw' => $elementString,
                'components' => $components,
            ];
        }

        return [
            'tag' => $tag,
            'elements' => $elements,
            'raw' => $segment.$delimiters['segment'],
        ];
    }

    private function unescapeSegment(string $segment, string $releaseChar): string
    {
        if ($releaseChar === '') {
            return $segment;
        }

        return preg_replace('#'.preg_quote($releaseChar, '#').'(.)#', '$1', $segment) ?? $segment;
    }

    /**
     * @param  array<string, mixed>  $unh
     */
    private function messageTypeFromUnh(array $unh): string
    {
        $messageId = $unh['elements'][2]['components'][0]
            ?? $unh['elements'][1]['components'][0]
            ?? '';

        return strtoupper(explode(':', (string) $messageId)[0] ?? $messageId);
    }

    private function segmentTag(string $segment): string
    {
        if (str_starts_with($segment, 'UNA')) {
            return 'UNA';
        }

        $pos = strpos($segment, '+');

        return $pos === false ? $segment : substr($segment, 0, $pos);
    }
}
