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
     * Fields that should be converted from "The Foo" to "Foo, The".
     */
    protected const SWAP_THE_FIELDS = ['InstituteName', 'TopParentCoName'];

    /**
     * The data file.
     *
     * @var string
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
     */
    public function getIterator(): Generator
    {
        yield from json_decode(file_get_contents($this->file), true, 512, JSON_THROW_ON_ERROR);
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

    // /**
    //  * Get the records in this data set sorted by a particular field.
    //  *
    //  * @param string $field The field to sort by.
    //  *
    //  * @return array The sorted records.
    //  */
    // public function getSorted(string $field): array
    // {
    //     $data = iterator_to_array($this);
    //     $swapThe = in_array($field, self::SWAP_THE_FIELDS);
    //     usort(
    //         $data,
    //         fn($a, $b) => strnatcasecmp(
    //             $swapThe === true ? preg_replace('/^(The) (.+)$/', '$2, $1', $a[$field]) : $a[$field],
    //             $swapThe === true ? preg_replace('/^(The) (.+)$/', '$2, $1', $b[$field]) : $b[$field],
    //         )
    //     );
    //     return $data;
    // }

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
