<?php

declare(strict_types=1);

namespace Tests\Unit\Edi;

use App\Domain\Edi\Mapping\MappingEngine;
use PHPUnit\Framework\TestCase;

final class MappingEngineTest extends TestCase
{
    public function test_applies_transforms_and_conditions(): void
    {
        $engine = new MappingEngine;

        $source = [
            'header' => ['po' => ' po-123 '],
            'meta' => ['type' => '850'],
        ];

        $rules = [
            [
                'source_path' => 'header.po',
                'target_path' => 'purchase_order.number',
                'transform' => 'trim',
                'conditions' => [
                    ['field' => 'meta.type', 'operator' => 'equals', 'value' => '850'],
                ],
            ],
            [
                'source_path' => 'header.po',
                'target_path' => 'purchase_order.number_upper',
                'transform' => 'uppercase',
            ],
            [
                'source_path' => 'missing',
                'target_path' => 'purchase_order.defaulted',
                'transform' => 'default',
                'transform_options' => ['value' => 'N/A'],
            ],
        ];

        $target = $engine->apply($source, $rules);

        $this->assertSame('po-123', $target['purchase_order']['number']);
        $this->assertSame(' PO-123 ', $target['purchase_order']['number_upper']);
        $this->assertSame('N/A', $target['purchase_order']['defaulted']);
    }
}
