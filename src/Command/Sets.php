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

use Ork\Beer\File;

/**
 * Command to list the available sets.
 */
class Sets extends AbstractCommand
{

    public function __invoke(array $args = []): void
    {
        $sets = (new File())->getAvailableSets();
        if (empty($sets) === true) {
            echo "No available sets. Run the 'update' command to download a fresh set.\n";
        } else {
            echo implode("\n", $sets), "\n";
        }
    }

    public function help(): string
    {
        return 'List the available sets.';
    }

}
