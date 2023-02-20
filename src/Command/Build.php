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

use Ork\Beer\Region;
use Ork\Beer\Set;

/**
 * Command to build the defined map regions and layers.
 */
class Build extends AbstractCommand
{

    /**
     * Run the command.
     *
     * @param array $args The arguments passed to the command, if any.
     *
     * @return void
     */
    public function __invoke(array $args = []): void
    {
        foreach ((new Region())->getRegions() as $name) {
            printf("Map: %s\n", $name);
            (new Kmz())([$name, (new Set(array_shift($args)))->getName(), $name]);
            echo "\n";
        }
    }

    /**
     * Output the help text for this command.
     *
     * @return string The help text for this command.
     */
    public function help(): string
    {
        return <<<EOS
            [<set>]
            Build the maps for a set. If no set is specified, the latest will
            be used.
            EOS;
    }

}
