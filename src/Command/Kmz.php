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
 * Command to output KMZ files.
 */
class Kmz extends Kml
{

    /**
     * Get the storage object for this file.
     *
     * @param string $file The output file.
     *
     * @return \Ork\Beer\Kmz
     */
    protected function getStorageObject(string $file): \Ork\Beer\Kmz
    {
        return new \Ork\Beer\Kmz($file);
    }

    /**
     * Output the help text for this command.
     *
     * @return string The help text for this command.
     */
    public function help(): string
    {
        return <<<EOS
            <output file> [<set>] [<region|state|country> [<region|state|country>...]]
            Generate KMZ files from a set. If no set is specified, the latest
            will be used.
            EOS;
    }

}
