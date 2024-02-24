<?php

namespace ExampleProject\unit\Customizer;

use CustomizeProject\Customizer;
use PHPUnit\Framework\TestCase;

class CustomizerTest extends TestCase
{

    /**
     * @test
     */
    public function itEnsuresValidComposerProjectNames()
    {
        $customizer = new Customizer();
        $pascalExample = 'CoolCorp/LeetNinja!';
        $this->assertEquals(
            'coolcorp/leetninja',
            $customizer->formatNameForPackagist($pascalExample)
        );
        $camelExample = 'coolCorp/LeetNinja!';
        $this->assertEquals(
            'coolcorp/leetninja',
            $customizer->formatNameForPackagist($camelExample)
        );
        $kebabExample = 'cool-corp@/leet-ninja!';
        $this->assertEquals(
            'cool-corp/leet-ninja',
            $customizer->formatNameForPackagist($kebabExample)
        );
        $numericExample = 'cool-corp1/leet-ninja4';
        $this->assertEquals(
            'cool-corp1/leet-ninja4',
            $customizer->formatNameForPackagist($numericExample)
        );
    }
}
