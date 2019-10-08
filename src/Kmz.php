<?php

/**
 * Ork Beer
 *
 * @package   Ork\Beer
 * @copyright 2019 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/AlexHowansky/ork-beer/blob/master/LICENSE MIT License
 * @link      https://github.com/AlexHowansky/ork-beer
 */

namespace Ork\Beer;

/**
 * KMZ generation class.
 */
class Kmz extends Kml
{

    /**
     * Finish up.
     *
     * @throws \RuntimeException On error.
     */
    public function __destruct()
    {
        parent::__destruct();
        $zip = new \ZipArchive();
        if ($zip->open($this->file . '.kmz', \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create KMZ file.');
        }
        if ($zip->addFile($this->file . '.kml') === false) {
            throw new \RuntimeException('Unable to add KML to KMZ file.');
        }
        if ($zip->close() === false) {
            throw new \RuntimeException('KMZ close failed.');
        }
        unlink($this->file . '.kml');
    }

}
