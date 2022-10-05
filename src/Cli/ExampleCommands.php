<?php

namespace ExampleProject\Cli;

use Robo\Symfony\ConsoleIO;

class ExampleCommands extends \Robo\Tasks
{
    /**
     * Multiply two numbers together
     *
     * @command multiply
     */
    public function multiply(ConsoleIO $io, $a, $b)
    {
        $model = new \ExampleProject\Example($a);
        $result = $model->multiply($b);

        $io->text("$a times $b is $result");
    }
}
