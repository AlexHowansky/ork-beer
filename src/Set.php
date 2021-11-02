<?php

/**
 * Ork Beer
 *
 * @package   Ork\Beer
 * @copyright 2019-2021 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/AlexHowansky/ork-beer/blob/master/LICENSE MIT License
 * @link      https://github.com/AlexHowansky/ork-beer
 */

namespace Ork\Beer;

/**
 * Methods to manipulate the data in a set.
 */
class Set implements \IteratorAggregate, \Countable
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
     * Filters to apply, if any.
     *
     * @var array
     */
    protected array $filters = [];

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
     * Add a filter.
     *
     * @param string $type   The type of filter.
     * @param string $field  The field to apply the filter to.
     * @param mixed  $value  The value to filter with.
     * @param bool   $invert True to invert the filter.
     *
     * @return Set Allow method chaining.
     */
    public function addFilter(string $type, string $field, $value, bool $invert = false): self
    {
        $this->filters[] = [
            'type' => $type,
            'field' => $field,
            'value' => $value,
            'invert' => $invert,
        ];
        return $this;
    }

    /**
     * Clear all previously set filters.
     *
     * @return Set Allow method chaining.
     */
    public function clearFilters(): self
    {
        $this->filters = [];
        return $this;
    }

    /**
     * Implement \Countable.
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
        return array_filter($this->clearFilters()->getDistinct('Country'));
    }

    /**
     * Get the distinct values for a field in this data set.
     *
     * @param string $field The field to get the distinct values for.
     *
     * @return array The distinct values.
     */
    public function getDistinct(string $field): array
    {
        $list = [];
        foreach ($this as $row) {
            $list[$row[$field]] = true;
        }
        ksort($list);
        return array_keys($list);
    }

    /**
     * Implement \IteratorAggregate
     *
     * @return \Generator
     *
     * @throws \RuntimeException If the requested filter is unknown.
     */
    public function getIterator(): \Generator
    {
        $csv = new \Ork\Csv\Reader([
            'file' => $this->file,
            'callbacks' => [
                'BreweryType' => 'strtolower',
                'StateProvince' => 'strtoupper',
            ],
        ]);
        foreach ($csv as $row) {
            foreach ($this->filters as $filter) {
                switch ($filter['type']) {
                    case 'in':
                        if (in_array($row[$filter['field']], $filter['value']) === false) {
                            continue 3;
                        }
                        break;
                    case '!in':
                        if (in_array($row[$filter['field']], $filter['value']) === true) {
                            continue 3;
                        }
                        break;
                    case 'match':
                        // @phpcs:ignore Ork.Operators.ComparisonOperatorUsage.NotAllowed
                        if ($row[$filter['field']] != $filter['value']) {
                            continue 3;
                        }
                        break;
                    case '!match':
                        // @phpcs:ignore Ork.Operators.ComparisonOperatorUsage.NotAllowed
                        if ($row[$filter['field']] == $filter['value']) {
                            continue 3;
                        }
                        break;
                    case 'regex':
                        if (preg_match($filter['value'], $row[$filter['field']]) !== 1) {
                            continue 3;
                        }
                        break;
                    case '!regex':
                        if (preg_match($filter['value'], $row[$filter['field']]) === 1) {
                            continue 3;
                        }
                        break;
                    default:
                        throw new \RuntimeException('Unknown filter type.');
                }
            }
            yield $row;
        }
    }

    /**
     * Get the name of this data set.
     *
     * @return string The name of this data set.
     */
    public function getName(): string
    {
        return basename($this->file, '.csv');
    }

    /**
     * Get the records in this data set sorted by a particular field.
     *
     * @param string $field The field to sort by.
     *
     * @return array The sorted records.
     */
    public function getSorted(string $field): array
    {
        $data = iterator_to_array($this);
        $swapThe = in_array($field, self::SWAP_THE_FIELDS);
        usort(
            $data,
            function ($a, $b) use ($field, $swapThe) {
                return strnatcasecmp(
                    $swapThe === true ? preg_replace('/^(The) (.+)$/', '$2, $1', $a[$field]) : $a[$field],
                    $swapThe === true ? preg_replace('/^(The) (.+)$/', '$2, $1', $b[$field]) : $b[$field],
                );
            }
        );
        return $data;
    }

    /**
     * Get the list of states represented by this data set.
     *
     * @return array The list of states represented by this data set.
     */
    public function getStates(): array
    {
        return array_filter(
            $this
                ->clearFilters()
                ->addFilter('regex', 'Country', '/^(United States|)$/')
                ->getDistinct('StateProvince')
        );
    }

}
