<?php

/**
 * Ork Beer
 *
 * @package   Ork\Beer
 * @copyright 2019-2022 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/AlexHowansky/ork-beer/blob/master/LICENSE MIT License
 * @link      https://github.com/AlexHowansky/ork-beer
 */

namespace Ork\Beer\Command;

/**
 * CLI command abstract.
 */
abstract class AbstractCommand implements CommandInterface
{

    /**
     * Get the name for this command.
     *
     * @return string The name for this command.
     */
    public function getCommandName(): string
    {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }

}
