<?php

namespace ExampleProject;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class ExampleCommandsTest extends TestCase implements CommandTesterInterface
{
    use CommandTesterTrait;

    /** @var string[] */
    protected $commandClasses;

    /**
     * Instantiate a new runner
     */
    public function setUp()
    {
        // Store the command classes we are going to test
        $this->commandClasses = [ \ExampleProject\Cli\ExampleCommands::class ];
        $this->setupCommandTester('TestFixtureApp', '1.0.1');
    }

    /**
     * Data provider for testExample.
     *
     * Return an array of arrays, each of which contains the parameter
     * values to be used in one invocation of the testExample test function.
     */
    public function exampleTestCommandParameters()
    {
        return [

            [
                '2 times 2 is 4', self::STATUS_OK,
                'multiply', 2, 2,
            ],

            [
                'Multiply two numbers together', self::STATUS_OK,
                'list',
            ],

            [
                'Not enough arguments (missing: "b").', self::STATUS_ERROR,
                'multiply', 7,
            ],
        ];
    }

    /**
     * Test our example class. Each time this function is called, it will
     * be passed data from the data provider function idendified by the
     * dataProvider annotation.
     *
     * @dataProvider exampleTestCommandParameters
     */
    public function testExampleCommands($expectedOutput, $expectedStatus, $variable_args)
    {
        // Set this to the path to a fixture configuration file if you'd like to use one.
        $configurationFile = false;

        // Create our argv array and run the command
        $argv = $this->argv(func_get_args());
        list($actualOutput, $statusCode) = $this->execute($argv, $this->commandClasses, $configurationFile);

        // Confirm that our output and status code match expectations
        $this->assertContains($expectedOutput, $actualOutput);
        $this->assertEquals($expectedStatus, $statusCode);
    }
}
