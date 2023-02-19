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

use Ork\Beer\Set;

/**
 * Command to list the states we have data for.
 */
class States extends AbstractCommand
{

    public function __invoke(array $args = []): void
    {
        echo implode("\n", (new Set(array_shift($args)))->getStates()), "\n";
    }

    public function help(): string
    {
        return <<<EOS
            [<set>]
            List the states in a specified set. If no set is specified, the
            latest will be used.
            EOS;
    }

}
