<?php

declare(strict_types=1);

namespace App\Domain\Edi\Services;

final class EdiValidationService
{
    /**
     * @param  array<string, mixed>  $parsed
     * @return list<string>
     */
    public function validate(string $standard, array $parsed): array
    {
        return match ($standard) {
            'x12' => $this->validateX12($parsed),
            'edifact' => $this->validateEdifact($parsed),
            default => ['Unsupported EDI standard: '.$standard],
        };
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @return list<string>
     */
    private function validateX12(array $parsed): array
    {
        $errors = [];

        if (($parsed['envelope']['isa'] ?? null) === null) {
            $errors[] = 'Missing ISA envelope segment.';
        }

        $groups = $parsed['envelope']['functional_groups'] ?? [];
        if ($groups === []) {
            $errors[] = 'No functional groups (GS/GE) found.';
        }

        foreach ($groups as $groupIndex => $group) {
            if (($group['gs'] ?? null) === null) {
                $errors[] = 'Functional group #'.($groupIndex + 1).' missing GS segment.';
            }
            if (($group['ge'] ?? null) === null) {
                $errors[] = 'Functional group #'.($groupIndex + 1).' missing GE segment.';
            }

            foreach ($group['transactions'] ?? [] as $txIndex => $transaction) {
                $errors = array_merge(
                    $errors,
                    $this->validateX12Transaction($transaction, $groupIndex + 1, $txIndex + 1)
                );
            }
        }

        if (($parsed['envelope']['iea'] ?? null) === null) {
            $errors[] = 'Missing IEA envelope segment.';
        }

        return $errors;
    }

    /**
     * @param  array<string, mixed>  $transaction
     * @return list<string>
     */
    private function validateX12Transaction(array $transaction, int $groupNum, int $txNum): array
    {
        $errors = [];
        $st = $transaction['st'] ?? null;
        $se = $transaction['se'] ?? null;
        $documentType = (string) ($transaction['document_type'] ?? '');

        if ($st === null) {
            $errors[] = "Group {$groupNum} transaction {$txNum}: missing ST segment.";
        } else {
            $control = $st['elements'][2] ?? '';
            if ($control === '') {
                $errors[] = "Group {$groupNum} transaction {$txNum}: ST control number is required.";
            }
        }

        if ($se === null) {
            $errors[] = "Group {$groupNum} transaction {$txNum}: missing SE segment.";
        } elseif ($st !== null) {
            $stControl = $st['elements'][2] ?? '';
            $seControl = $se['elements'][2] ?? '';
            if ($stControl !== '' && $seControl !== '' && $stControl !== $seControl) {
                $errors[] = "Group {$groupNum} transaction {$txNum}: ST/SE control numbers must match.";
            }
        }

        $required = match ($documentType) {
            '850' => ['BEG', 'CTT'],
            '855' => ['BAK'],
            '856' => ['BSN'],
            '810' => ['BIG', 'TDS'],
            '997' => ['AK1', 'AK9'],
            default => [],
        };

        $segmentIds = array_map(
            static fn (array $segment): string => $segment['id'],
            $transaction['segments'] ?? []
        );

        foreach ($required as $segmentId) {
            if (! in_array($segmentId, $segmentIds, true)) {
                $errors[] = "Document {$documentType}: required segment {$segmentId} not found.";
            }
        }

        return $errors;
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @return list<string>
     */
    private function validateEdifact(array $parsed): array
    {
        $errors = [];

        if (($parsed['envelope']['unb'] ?? null) === null) {
            $errors[] = 'Missing UNB interchange header.';
        }

        foreach ($parsed['envelope']['messages'] ?? [] as $index => $message) {
            if (($message['unh'] ?? null) === null) {
                $errors[] = 'Message #'.($index + 1).': missing UNH.';
            }
            if (($message['unt'] ?? null) === null) {
                $errors[] = 'Message #'.($index + 1).': missing UNT.';
            }

            $documentType = strtoupper((string) ($message['document_type'] ?? ''));
            $tags = array_map(
                static fn (array $segment): string => $segment['tag'],
                $message['segments'] ?? []
            );

            $required = match ($documentType) {
                'ORDERS', 'ORDRSP', 'DESADV', 'INVOIC' => ['BGM'],
                'CONTRL' => ['UCI'],
                default => [],
            };

            foreach ($required as $tag) {
                if (! in_array($tag, $tags, true)) {
                    $errors[] = "EDIFACT {$documentType}: required segment {$tag} not found.";
                }
            }
        }

        if (($parsed['envelope']['unz'] ?? null) === null) {
            $errors[] = 'Missing UNZ interchange trailer.';
        }

        return $errors;
    }
}
