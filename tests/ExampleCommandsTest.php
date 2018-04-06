<?php

namespace ExampleProject;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class ExampleCommandsTest extends TestCase
{
    /** @var string[] */
    protected $commandClasses;

    /** @var string */
    protected $appName;

    /** @var string */
    protected $appVersion;

    const STATUS_OK = 0;
    const STATUS_ERROR = 1;
    const NOT_ENOUGH_AGUMENTS_ERROR = <<<EOT
Not enough arguments (missing: "b").


multiply [-h|--help] [-q|--quiet] [-v|vv|vvv|--verbose] [-V|--version] [--ansi] [--no-ansi] [-n|--no-interaction] [--simulate] [--progress-delay PROGRESS-DELAY] [-D|--define DEFINE] [--] <command> <a> <b>
EOT;

    /**
     * Instantiate a new runner
     */
    public function setUp()
    {
        // Store the command classes we are going to test
        $this->commandClasses = [ \ExampleProject\Cli\ExampleCommands::class ];

        // Define our invariants for our test
        $this->appName = 'TestFixtureApp';
        $this->appVersion = '1.0.1';
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
                '3 times 3 is 9', self::STATUS_OK,
                'multiply', 3, 3,
            ],

            [
                '7 times 8 is 56', self::STATUS_OK,
                'multiply', 7, 8,
            ],

            [
                self::NOT_ENOUGH_AGUMENTS_ERROR, self::STATUS_ERROR,
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
        // Create our argv array and run the command
        $argv = $this->argv(func_get_args());
        list($actualOutput, $statusCode) = $this->execute($argv);

        // Confirm that our output and status code match expectations
        $this->assertContains($this->squashSpaces($expectedOutput), $this->squashSpaces($actualOutput));
        $this->assertEquals($expectedStatus, $statusCode);
    }

    /**
     * Prepare our $argv array; put the app name in $argv[0] followed by
     * the command name and all command arguments and options.
     */
    protected function argv($functionParameters)
    {
        $argv = $functionParameters;
        array_shift($argv);
        array_shift($argv);
        array_unshift($argv, $this->appName);

        return $argv;
    }

    /**
     * Simulated front controller
     */
    protected function execute($argv)
    {
        // Define a global output object to capture the test results
        $output = new BufferedOutput();

        // We can only call `Runner::execute()` once; then we need to tear down.
        $runner = new \Robo\Runner($this->commandClasses);
        $statusCode = $runner->execute($argv, $this->appName, $this->appVersion, $output);
        \Robo\Robo::unsetContainer();

        // Return the output and status code.
        $actualOutput = trim($output->fetch());
        return [$actualOutput, $statusCode];
    }

    /**
     * Allow for some variation in whitespace (e.g. Windows vs Linux EOL, etc.)
     */
    protected function squashSpaces($text)
    {
        $text = preg_replace('#[ \t]+#m', ' ', $text);
        $text = preg_replace('#[ \t]*$#m', '', $text);
        $text = preg_replace('#^[ \t]*#m', '', $text);
        $text = preg_replace("#^[ \t\n\r]+$#m", '', $text);
        $text = preg_replace("#[\n\r]+#m", "\n", $text);
        $text = preg_replace("#^[ \t\n\r]+$#m", '', $text);

        return $text;
    }
}
