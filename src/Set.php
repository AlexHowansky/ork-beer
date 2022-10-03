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

use Countable;
use Generator;
use IteratorAggregate;
use RuntimeException;

/**
 * Methods to manipulate the data in a set.
 */
class Set implements Countable, IteratorAggregate
{

    /**
     * The data file.
     */
    protected string $file;

    /**
     * Constructor.
     *
     * @param string $set The set to work with. Leave empty to use the latest.
     */
    public function __construct(string $set = null)
    {
        $this->file = (new File())->get($set);
    }

    /**
     * Get the records in this data set sorted by name.
     *
     * @return array The sorted records.
     */
    public function byName(): array
    {
        $data = iterator_to_array($this);
        usort(
            $data,
            fn(array $a, array $b) => strnatcasecmp(
                preg_replace('/^The\s+/i', '', $a['Name'] ?? null),
                preg_replace('/^The\s+/i', '', $b['Name'] ?? null)
            )
        );
        return $data;
    }

    /**
     * Implement Countable interface.
     *
     * @return int The number of records in this data set.
     */
    public function count(): int
    {
        return count(iterator_to_array($this));
    }

    /**
     * Get the list of countries represented by this data set.
     *
     * @return array The list of countries represented by this data set.
     */
    public function getCountries(): array
    {
        $countries = [];
        foreach ($this as $record) {
            $countries[$record['BillingAddress']['country'] ?? null] = true;
        }
        ksort($countries);
        return array_filter(array_keys($countries));
    }

    /**
     * Implement IteratorAggregate interface.
     *
     * @return Generator<array>
     *
     * @throws RuntimeException If the set file is not valid JSON.
     */
    public function getIterator(): Generator
    {
        $data = json_decode((string) file_get_contents($this->file), true, 512, JSON_THROW_ON_ERROR);
        if (is_array($data) !== true) {
            throw new RuntimeException('Invalid data in set.');
        }
        yield from $data;
    }

    /**
     * Get the name of this data set.
     *
     * @return string The name of this data set.
     */
    public function getName(): string
    {
        return basename($this->file, '.json');
    }

    /**
     * Get the list of states represented by this data set.
     *
     * @return array The list of states represented by this data set.
     */
    public function getStates(): array
    {
        $states = [];
        foreach ($this as $record) {
            if (($record['BillingAddress']['countryCode'] ?? null) === 'US') {
                $states[$record['BillingAddress']['state'] ?? null] = true;
            }
        }
        ksort($states);
        return array_filter(array_keys($states));
    }

}
