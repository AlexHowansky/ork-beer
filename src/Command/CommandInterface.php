<?php

/**
 * Ork Beer
 *
 * @package   Ork\Beer
 * @copyright 2019-2023 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/AlexHowansky/ork-beer/blob/master/LICENSE MIT License
 * @link      https://github.com/AlexHowansky/ork-beer
 */

namespace Ork\Beer\Command;

/**
 * CLI command interface.
 */
interface CommandInterface
{

    /**
     * Run the command.
     *
     * @param array<string> $args The arguments passed to the command, if any.
     */
    public function __invoke(array $args = []): void;

    /**
     * Return the help text for this command.
     *
     * @return string The help text for this command.
     */
    public function help(): string;

    /**
     * Return the short name for this command.
     *
     * @return string The short name for this command.
     */
    public function name(): string;

}
