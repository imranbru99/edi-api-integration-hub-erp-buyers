<?php

declare(strict_types=1);

namespace Tests\Unit\Edi;

use App\Domain\Edi\Parsers\EdifactParser;
use PHPUnit\Framework\TestCase;

final class EdifactParserTest extends TestCase
{
    private EdifactParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new EdifactParser;
    }

    public function test_parses_orders_fixture(): void
    {
        $raw = file_get_contents(dirname(__DIR__, 2).'/Fixtures/edi/edifact_orders_sample.edi');
        $this->assertNotFalse($raw);

        $parsed = $this->parser->parse($raw);

        $this->assertSame('edifact', $parsed['standard']);
        $this->assertNotNull($parsed['envelope']['unb']);
        $this->assertNotNull($parsed['envelope']['unz']);

        $message = $parsed['transactions'][0];
        $this->assertSame('ORDERS', $message['document_type']);
        $this->assertSame('PO-EDIFACT-001', $message['header']['bgm'][2]['components'][0] ?? null);
        $this->assertCount(2, $message['lines']);
        $this->assertSame('WIDGET-A', $message['lines'][0]['lin'][3]['components'][0] ?? null);
        $this->assertSame('25', $message['lines'][0]['qty'][1]['components'][1] ?? null);
    }
}
