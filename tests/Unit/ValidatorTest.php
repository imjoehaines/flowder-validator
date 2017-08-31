<?php

namespace Imjoehaines\Flowder\Validator\Test;

use PHPUnit\Framework\TestCase;
use Imjoehaines\Flowder\Validator\Result;
use Imjoehaines\Flowder\Validator\Validator;

class ValidatorTest extends TestCase
{
    public function testItReturnsValidResultForValidData()
    {
        $validator = new Validator();

        $data = [
            [
                'a' => 1,
                'b' => 2,
            ],
            [
                'a' => 3,
                'b' => 4,
            ],
        ];

        $expected = Result::valid();

        $actual = $validator->validate('table', $data);

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsValidResultForEmptyData()
    {
        $validator = new Validator();

        $data = [
            [],
            [],
        ];

        $expected = Result::valid();

        $actual = $validator->validate('table', $data);

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsInvalidResultForDataWithAnExtraColumn()
    {
        $validator = new Validator();

        $data = [
            [
                'a' => 1,
                'b' => 2,
            ],
            [
                'a' => 3,
                'b' => 4,
                'c' => 5,
            ],
        ];

        $expected = Result::invalid(['"table" — Fixture at index 1 has extra columns: "c"']);

        $actual = $validator->validate('table', $data);

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsInvalidResultForDataWithMultipleExtraColumns()
    {
        $validator = new Validator();

        $data = [
            [
                'a' => 1,
                'b' => 2,
            ],
            [
                'a' => 3,
                'b' => 4,
                'c' => 5,
                'd' => 6,
            ],
        ];

        $expected = Result::invalid(['"table" — Fixture at index 1 has extra columns: "c", "d"']);

        $actual = $validator->validate('table', $data);

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsInvalidResultForDataWithAMissingColumn()
    {
        $validator = new Validator();

        $data = [
            [
                'a' => 1,
                'b' => 2,
            ],
            [
                'a' => 3,
            ],
        ];

        $expected = Result::invalid(['"table" — Fixture at index 1 is missing columns: "b"']);

        $actual = $validator->validate('table', $data);

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsInvalidResultForDataWithMultipleMissingColumns()
    {
        $validator = new Validator();

        $data = [
            [
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ],
            [
                'a' => 4,
            ],
        ];

        $expected = Result::invalid(['"table" — Fixture at index 1 is missing columns: "b", "c"']);

        $actual = $validator->validate('table', $data);

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsInvalidResultForDataWithBothExtraAndMissingColumns()
    {
        $validator = new Validator();

        $data = [
            [
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ],
            [
                'a' => 4,
                'b' => 5,
                'c' => 6,
                'd' => 7,
                'e' => 8,
            ],
            [
                'a' => 9,
            ],
        ];

        $expected = Result::invalid([
            '"table" — Fixture at index 1 has extra columns: "d", "e"',
            '"table" — Fixture at index 2 is missing columns: "b", "c"'
        ]);

        $actual = $validator->validate('table', $data);

        $this->assertEquals($expected, $actual);
    }
}
