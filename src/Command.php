<?php

/**
 * Ork Beer
 *
 * @package   Ork\Beer
 * @copyright 2019-2023 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/AlexHowansky/ork-beer/blob/master/LICENSE MIT License
 * @link      https://github.com/AlexHowansky/ork-beer
 */

namespace Ork\Beer;

use Ork\Beer\Command\Build;
use Ork\Beer\Command\CommandInterface;
use Ork\Beer\Command\Countries;
use Ork\Beer\Command\Info;
use Ork\Beer\Command\Kml;
use Ork\Beer\Command\Kmz;
use Ork\Beer\Command\Regions;
use Ork\Beer\Command\Sets;
use Ork\Beer\Command\States;
use Ork\Beer\Command\Update;

/**
 * CLI controller.
 */
class Command
{

    /**
     * How we were invoked.
     */
    protected string $self;

    /**
     * Process the command.
     */
    public function __invoke(): void
    {
        $this->self = array_shift($_SERVER['argv']);
        $command = array_shift($_SERVER['argv']);
        if (empty($command) === true) {
            $this->help('No command specified.');
        }
        foreach ($this->getCommands() as $commandObj) {
            if (strtoupper($command) === strtoupper($commandObj->name())) {
                $commandObj($_SERVER['argv']);
                exit;
            }
        }
        $this->help('Unknown command specified.');
    }

    /**
     * Iterate over all the available commands.
     *
     * @return array<CommandInterface> The available commands.
     */
    protected function getCommands(): array
    {
        return [
            new Build(),
            new Countries(),
            new Info(),
            new Kml(),
            new Kmz(),
            new Regions(),
            new Sets(),
            new States(),
            new Update(),
        ];
    }

    /**
     * Output the help.
     *
     * @param string $message An additional message to output.
     */
    protected function help(string $message): void
    {
        fprintf(STDERR, "ERROR: %s\n\n", $message);
        fprintf(STDERR, "Usage: %s <command>\n\n", $this->self);
        fprintf(STDERR, "Commands:\n\n");
        $length = max(
            array_map(
                fn($c) => strlen($c->name()),
                $this->getCommands()
            )
        ) + 4;
        foreach ($this->getCommands() as $command) {
            foreach (explode("\n", $command->help()) as $index => $line) {
                fprintf(
                    STDERR,
                    "%s    %s\n",
                    $index === 0
                        ? str_pad($command->name(), $length, ' ', STR_PAD_LEFT)
                        : str_repeat(' ', $length),
                    $line
                );
            }
            fprintf(STDERR, "\n");
        }
        exit(1);
    }

}
