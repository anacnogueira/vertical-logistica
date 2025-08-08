<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\LineParser;

class LineParserTest extends TestCase
{

    protected $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new LineParser();
    }

    #[Test]
    public function test_it_parses_a_valid_line_correctly():void
    {
        $line = "0000000070                              Palmer Prosacco00000007530000000003     1836.7420210308";

        $expected = [
            'user_id' => 70,
            'user_name' => 'Palmer Prosacco',
            'order_id' => 753,
            'product_id' => 3,
            'value' => '1836.74',
            'date' => '2021-03-08',
        ];

        $this->assertEquals($expected, $this->parser->parseLine($line));
    }

    #[Test]
    public function test_it_handles_another_valid_line_correctly(): void
    {
        $line = "0000000077                         Mrs. Stephen Trantow00000008480000000004      1689.020210325";

        $expected = [
            'user_id' => 77,
            'user_name' => 'Mrs. Stephen Trantow',
            'order_id' => 848,
            'product_id' => 4,
            'value' => '1689.00',
            'date' => '2021-03-25',
        ];

        $this->assertEquals($expected, $this->parser->parseLine($line));
    }

    #[Test]
    public function test_it_returns_empty_array_for_empty_line(): void
    {
        $line = "";
        $this->assertEmpty($this->parser->parseLine($line));
    }

    #[Test]
    public function test_it_returns_empty_array_for_line_with_only_whitespace(): void
    {
        $line = "    ";
        $this->assertEmpty($this->parser->parseLine($line));
    }

    #[Test]
    public function test_it_formats_value_with_two_decimal_places(): void
    {
        $line = "0000000001                                 TestUser00000000010000000001     10.0020230101";
        $result = $this->parser->parseLine($line);
        $this->assertEquals('10.00', $result['value']);

        $line = "0000000001                                 TestUser00000000010000000001     5.520230101";
        $result = $this->parser->parseLine($line);
        $this->assertEquals('5.52', $result['value']);
    }
}
