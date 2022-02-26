<?php

/**
 * Ork Beer
 *
 * @package   Ork\Beer
 * @copyright 2019-2022 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/AlexHowansky/ork-beer/blob/master/LICENSE MIT License
 * @link      https://github.com/AlexHowansky/ork-beer
 */

namespace Ork\Beer;

/**
 * Data file management class.
 */
class File
{

    /**
     * If we receive fewer than this many records, we'll consider it an error.
     */
    protected const THRESHOLD = 10000;

    /**
     * The URL for the JSON content.
     */
    protected const URL =
        'https://www.brewersassociation.org/wp-content/themes/ba2019/json-store/breweries/breweries.json';

    /**
     * Get the verified file name for a given data set. If no set name is
     * provided, get the latest one.
     *
     * @param string $set The data set to get the file name for.
     *
     * @return string The verified file name for the given data set.
     *
     * @throws \RuntimeException If the requested set does not exist.
     */
    public function get(string $set = null): string
    {
        $file = $this->getFileNameForSet($set ?? $this->getLatestSet());
        if (file_exists($file) === false) {
            throw new \RuntimeException('No such data set.');
        }
        return $file;
    }

    /**
     * Get a list of the available data sets.
     *
     * @return array A list of the available data sets.
     */
    public function getAvailableSets(): array
    {
        $sets = [];
        foreach (new \DirectoryIterator($this->getDataDirectory()) as $file) {
            if (
                $file->isDot() === false &&
                $file->isFile() === true &&
                $file->getExtension() === 'csv'
            ) {
                $sets[] = $file->getBaseName('.csv');
            }
        }
        sort($sets);
        return $sets;
    }

    /**
     * Get the data directory.
     *
     * @return string The data directory.
     *
     * @throws \RuntimeException If the data directory does not exist.
     */
    protected function getDataDirectory(): string
    {
        $dir = realpath(__DIR__ . '/../data');
        if ($dir === false) {
            throw new \RuntimeException('Data directory does not exist.');
        }
        return $dir;
    }

    /**
     * Get the unverified file name for a given set.
     *
     * @param string $set The data set to get the file name for.
     *
     * @return string The unverified file name for the given data set.
     */
    protected function getFileNameForSet(string $set): string
    {
        return sprintf('%s/%s.csv', $this->getDataDirectory(), $set);
    }

    /**
     * Scan the data directory and find the most recent data set.
     *
     * @return string The most recent data set.
     *
     * @throws \RuntimeException If there is no local data.
     */
    public function getLatestSet(): string
    {
        $sets = $this->getAvailableSets();
        if (empty($sets) === true) {
            throw new \RuntimeException('No data. Run `beer update` to get new data.');
        }
        return array_pop($sets);
    }

    /**
     * Get the current data from our source.
     *
     * @return array The data.
     *
     * @throws \RuntimeException On error.
     */
    protected function getSnapshot(): array
    {
        $json = file_get_contents(self::URL);
        if (empty($json) === true) {
            throw new \RuntimeException('JSON query produced no output.');
        }
        $data = json_decode(json_decode($json, true), true);
        if (empty($data) === true) {
            throw new \RuntimeException('JSON decode failed.');
        }
        if (is_array($data) === false || array_key_exists('ResultData', $data) === false) {
            throw new \RuntimeException('JSON decode produced unexpected data.');
        }
        if (count($data['ResultData']) < self::THRESHOLD) {
            throw new \RuntimeException('Record count suspiciously low, aborting.');
        }
        return $data['ResultData'];
    }

    /**
     * Get the name for a file that would be created today.
     *
     * @return string The name for a file that would be created today.
     */
    protected function getTodaysFileName(): string
    {
        return $this->getFileNameForSet($this->getTodaysSet());
    }

    /**
     * Get the name for a set that would be created today.
     *
     * @return string The name for a set that would be created today.
     */
    protected function getTodaysSet(): string
    {
        return date('Ymd');
    }

    /**
     * Pull the latest data from our source and save it locally.
     *
     * @return int The number of rows pulled.
     *
     * @throws \RuntimeException If a data file already exists for today.
     */
    public function update(): int
    {
        $file = $this->getTodaysFileName();
        if (file_exists($file) === true) {
            throw new \RuntimeException('Set ' . $this->getTodaysSet() . ' already exists.');
        }
        $csv = new \Ork\Csv\Writer(['file' => $file]);
        $rows = 0;
        foreach ($this->getSnapshot() as $row) {
            ++$rows;
            $csv->write($row);
        }
        return $rows;
    }

}
