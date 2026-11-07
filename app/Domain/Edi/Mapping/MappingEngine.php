<?php

declare(strict_types=1);

namespace App\Domain\Edi\Mapping;

use Illuminate\Support\Arr;

final class MappingEngine
{
    /**
     * @param  array<string, mixed>  $source
     * @param  iterable<int, array<string, mixed>>  $rules
     * @return array<string, mixed>
     */
    public function apply(array $source, iterable $rules): array
    {
        $target = [];

        foreach ($rules as $rule) {
            if (! $this->conditionsMet($source, $rule)) {
                continue;
            }

            $value = $this->resolveSourceValue($source, (string) ($rule['source_path'] ?? ''));
            $value = $this->applyTransform($value, $rule);

            if (($rule['target_path'] ?? '') === '') {
                continue;
            }

            Arr::set($target, (string) $rule['target_path'], $value);
        }

        return $target;
    }

    /**
     * @param  array<string, mixed>  $source
     * @param  array<string, mixed>  $rule
     */
    private function conditionsMet(array $source, array $rule): bool
    {
        $conditions = $rule['conditions'] ?? [];
        if ($conditions === []) {
            return true;
        }

        foreach ($conditions as $condition) {
            $field = (string) ($condition['field'] ?? '');
            $operator = (string) ($condition['operator'] ?? 'equals');
            $expected = $condition['value'] ?? null;
            $actual = Arr::get($source, $field);

            $ok = match ($operator) {
                'not_equals' => $actual != $expected,
                'contains' => is_string($actual) && str_contains($actual, (string) $expected),
                'empty' => $actual === null || $actual === '',
                'not_empty' => $actual !== null && $actual !== '',
                default => $actual == $expected,
            };

            if (! $ok) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $source
     */
    private function resolveSourceValue(array $source, string $path): mixed
    {
        if ($path === '') {
            return null;
        }

        return Arr::get($source, $path);
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    private function applyTransform(mixed $value, array $rule): mixed
    {
        $transform = (string) ($rule['transform'] ?? '');
        $options = $rule['transform_options'] ?? [];

        return match ($transform) {
            'uppercase' => is_string($value) ? strtoupper($value) : $value,
            'lowercase' => is_string($value) ? strtolower($value) : $value,
            'trim' => is_string($value) ? trim($value) : $value,
            'default' => ($value === null || $value === '') ? ($options['value'] ?? '') : $value,
            'date_format' => $this->transformDate($value, (string) ($options['format'] ?? 'Y-m-d')),
            'number_format' => $this->transformNumber($value, (int) ($options['decimals'] ?? 2)),
            'lookup' => $this->transformLookup($value, $options['map'] ?? []),
            'concat' => $this->transformConcat($value, $options),
            'substring' => $this->transformSubstring($value, $options),
            default => $value,
        };
    }

    private function transformDate(mixed $value, string $format): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $string = (string) $value;
        $timestamp = strtotime($string);

        if ($timestamp === false && strlen($string) === 8 && ctype_digit($string)) {
            $timestamp = strtotime(substr($string, 0, 4).'-'.substr($string, 4, 2).'-'.substr($string, 6, 2));
        }

        return $timestamp === false ? null : date($format, $timestamp);
    }

    private function transformNumber(mixed $value, int $decimals): mixed
    {
        if (! is_numeric($value)) {
            return $value;
        }

        return number_format((float) $value, $decimals, '.', '');
    }

    /**
     * @param  array<string|int, mixed>  $map
     */
    private function transformLookup(mixed $value, array $map): mixed
    {
        $key = (string) $value;

        return $map[$key] ?? $map['__default'] ?? $value;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function transformConcat(mixed $value, array $options): string
    {
        $parts = $options['parts'] ?? [];
        if ($parts === []) {
            return is_scalar($value) ? (string) $value : '';
        }

        return implode((string) ($options['glue'] ?? ''), array_map(
            static fn ($part) => is_scalar($part) ? (string) $part : '',
            $parts
        ));
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function transformSubstring(mixed $value, array $options): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $start = (int) ($options['start'] ?? 0);
        $length = isset($options['length']) ? (int) $options['length'] : null;

        return $length === null ? substr($value, $start) : substr($value, $start, $length);
    }
}
