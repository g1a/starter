<?php

namespace ExampleProject;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function exampleTestValues()
    {
        return [
            [4, 2, 2,],
            [9, 3, 3,],
            [56, 7, 8,],
        ];
    }

    /**
     * @dataProvider exampleTestValues
     */
    public function testExample($expected, $constructor_parameter, $value)
    {
        $example = new Example($constructor_parameter);
        $this->assertEquals($expected, $example->multiply($value));
    }
}
