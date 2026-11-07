<?php

declare(strict_types=1);

namespace Tests\Unit\Edi;

use App\Domain\Edi\Parsers\X12Parser;
use PHPUnit\Framework\TestCase;

final class X12ParserTest extends TestCase
{
    private X12Parser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new X12Parser;
    }

    public function test_parses_850_fixture_with_lines_and_envelope(): void
    {
        $raw = file_get_contents(dirname(__DIR__, 2).'/Fixtures/edi/x12_850_sample.edi');
        $this->assertNotFalse($raw);

        $parsed = $this->parser->parse($raw);

        $this->assertSame('x12', $parsed['standard']);
        $this->assertNotNull($parsed['envelope']['isa']);
        $this->assertNotNull($parsed['envelope']['iea']);

        $transaction = $parsed['transactions'][0];
        $this->assertSame('850', $transaction['document_type']);
        $this->assertSame('0001', $transaction['control_number']);
        $this->assertSame('PO-987654', $transaction['header']['beg'][3] ?? null);
        $this->assertCount(2, $transaction['lines']);
        $this->assertSame('WIDGET-A', $transaction['lines'][0]['po1'][7] ?? null);
        $this->assertSame('2', $transaction['header']['ctt'][1] ?? null);
    }

    public function test_parses_997_functional_acknowledgment(): void
    {
        $raw = <<<'EDI'
ISA*00*          *00*          *ZZ*SENDER         *ZZ*RECEIVER       *231107*1200*U*00401*000000001*0*P*>~
GS*FA*SENDER*RECEIVER*20231107*1200*1*X*004010~
ST*997*0001~
AK1*PO*1~
AK2*850*0001~
AK5*A~
AK9*A*1*1*1~
SE*6*0001~
GE*1*1~
IEA*1*000000001~
EDI;

        $parsed = $this->parser->parse($raw);
        $transaction = $parsed['transactions'][0];

        $this->assertSame('997', $transaction['document_type']);
        $this->assertSame('PO', $transaction['header']['ak1'][1] ?? null);
        $this->assertSame('850', $transaction['acknowledgments'][0]['ak2'][1] ?? null);
        $this->assertSame('A', $transaction['header']['ak9'][1] ?? null);
    }
}
