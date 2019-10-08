<?php

/**
 * Ork Beer
 *
 * @package   Ork\Beer
 * @copyright 2019 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/AlexHowansky/ork-beer/blob/master/LICENSE MIT License
 * @link      https://github.com/AlexHowansky/ork-beer
 */

namespace Ork\Beer;

/**
 * CLI controller.
 */
class Command
{

    /**
     * How we were invoked.
     *
     * @var string
     */
    protected string $self;

    /**
     * Process the command.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $this->self = array_shift($_SERVER['argv']);
        $command = array_shift($_SERVER['argv']);
        if (empty($command) === true) {
            $this->help('No command specified.');
        }
        $class = __NAMESPACE__ . '\Command\\' . ucfirst(strtolower($command));
        if (class_exists($class) === false) {
            $this->help('Unknown command specified.');
        }
        (new $class)($_SERVER['argv']);
    }

    /**
     * Iterate over all the available commands.
     *
     * @return array
     */
    protected function getCommands(): array
    {
        $commands = [];
        foreach (new \DirectoryIterator(__DIR__ . '/Command') as $file) {
            if (
                $file->isFile() === true &&
                $file->getFileName() !== 'AbstractCommand.php' &&
                $file->getFileName() !== 'CommandInterface.php'
            ) {
                $class = __NAMESPACE__ . '\Command\\' . basename($file->getFileName(), '.php');
                $object = new $class();
                $commands[$object->getCommandName()] = $object;
            }
        }
        ksort($commands);
        return array_values($commands);
    }

    /**
     * Output the help.
     *
     * @param string $message An additional message to output.
     *
     * @return void
     */
    protected function help(string $message): void
    {
        printf("ERROR: %s\n\n", $message);
        printf("Usage: %s <command>\n\n", $this->self);
        echo "Commands:\n\n";
        $length = max(
            array_map(
                function ($c) {
                    return strlen($c->getCommandName());
                },
                $this->getCommands()
            )
        ) + 4;
        foreach ($this->getCommands() as $command) {
            foreach (explode("\n", $command->help()) as $index => $line) {
                printf(
                    "%s    %s\n",
                    $index === 0
                        ? str_pad($command->getCommandName(), $length, ' ', STR_PAD_LEFT)
                        : str_repeat(' ', $length),
                    $line
                );
            }
            echo "\n";
        }
        exit(1);
    }

}
