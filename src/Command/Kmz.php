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

use Ork\Beer\KmzBuilder;

/**
 * Command to output KMZ files.
 */
class Kmz extends Kml
{

    /**
     * Get the storage object for this file.
     *
     * @param string $file The output file.
     */
    protected function getStorageObject(string $file): KmzBuilder
    {
        return new KmzBuilder($file);
    }

    /**
     * @inheritdoc
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
