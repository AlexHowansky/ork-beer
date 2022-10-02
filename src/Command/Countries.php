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

use Ork\Beer\Set;

/**
 * Command to list the countries we have data for.
 */
class Countries extends AbstractCommand
{

    public function __invoke(array $args = []): void
    {
        echo implode("\n", (new Set(array_shift($args)))->getCountries()), "\n";
    }

    public function help(): string
    {
        return <<<EOS
            [<set>]
            List the countries in a specified set. If no set is specified, the
            latest will be used.
            EOS;
    }

}
