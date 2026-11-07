<?php

declare(strict_types=1);

namespace Tests\Unit\Edi;

use App\Domain\Edi\Generators\X12Generator;
use App\Domain\Edi\Parsers\X12Parser;
use PHPUnit\Framework\TestCase;

final class X12GeneratorTest extends TestCase
{
    public function test_generates_parseable_850(): void
    {
        $generator = new X12Generator;
        $parser = new X12Parser;

        $payload = $generator->generate([
            'metadata' => [
                'sender_id' => 'SENDER123',
                'receiver_id' => 'RECEIVER456',
                'isa_control' => '000000905',
                'gs_control' => '905',
            ],
            'transaction' => [
                'document_type' => '850',
                'control_number' => '0001',
                'header' => [
                    'beg' => [1 => '00', 2 => 'SA', 3 => 'PO-GEN-1', 5 => '20231107'],
                ],
                'lines' => [
                    ['po1' => [1 => '1', 2 => '5', 3 => 'EA', 4 => '10.00', 6 => 'VP', 7 => 'SKU-1']],
                ],
            ],
        ]);

        $this->assertStringContainsString('ST*850*0001~', $payload);
        $this->assertStringContainsString('BEG*00*SA*PO-GEN-1', $payload);
        $this->assertStringContainsString('PO1*1*5*EA*10.00**VP*SKU-1~', $payload);

        $parsed = $parser->parse($payload);
        $this->assertSame('850', $parsed['transactions'][0]['document_type']);
        $this->assertSame('PO-GEN-1', $parsed['transactions'][0]['header']['beg'][3] ?? null);
    }
}
