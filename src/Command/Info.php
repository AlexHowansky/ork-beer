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
 * Command to print some information about a set.
 */
class Info extends AbstractCommand
{

    public function __invoke(array $args = []): void
    {
        $set = new Set(array_shift($args));
        printf(
            "Set %s contains %s breweries from %d countries.\n",
            $set->getName(),
            number_format(count($set)),
            number_format(count($set->getCountries()))
        );
    }

    public function help(): string
    {
        return <<<EOS
            [<set>]
            Print some statistics about a set. If no set is specified, the
            latest will be used.
            EOS;
    }

}
