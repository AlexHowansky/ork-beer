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

/**
 * Command to list the map regions and layers.
 */
class Regions extends AbstractCommand
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
        $region = new Region();
        foreach ($region->getRegions() as $name) {
            printf("Region: %s\n", $name);
            foreach ($region->getLayersInRegion($name) as $layer => $states) {
                printf("  Layer: %s\n", $layer);
                printf("    States: %s\n", join(', ', $states));
            }
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
            List the map region and layer information.
            EOS;
    }

}
