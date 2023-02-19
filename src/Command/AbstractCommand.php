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

use ReflectionClass;

/**
 * CLI command abstract.
 */
abstract class AbstractCommand implements CommandInterface
{

    public function name(): string
    {
        return strtolower((new ReflectionClass($this))->getShortName());
    }

}
