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

use Exception;
use Generator;
use Ork\Beer\KmlBuilder;
use Ork\Beer\Set;
use Ork\Beer\State;
use RuntimeException;

/**
 * Command to output KML files.
 */
class Kml extends AbstractCommand
{

    protected const LAYER_LIMIT = 2000;

    protected const SKIP_TYPES = ['Brewery In Planning', 'Office only location'];

    protected Set $set;

    protected KmlBuilder $store;

    /**
     * Run the command.
     *
     * @param array $args The arguments passed to the command, if any.
     *
     * @return void
     *
     * @throws RuntimeException On error.
     */
    public function __invoke(array $args = []): void
    {
        $output = array_shift($args);
        if (empty($output) === true) {
            throw new RuntimeException('Must specify output file.');
        }
        if (is_writable(dirname($output)) === false) {
            throw new RuntimeException('Specified output directory is not writable.');
        }
        $this->set = new Set(preg_match('/^\d{8}$/', $args[0] ?? null) === 1 ? array_shift($args) : null);
        printf("Using set: %s\n", $this->set->getName());
        $this->store = $this->getStorageObject($output);
        foreach ($this->expandArgs($args) as $layer => $filters) {
            $this->layer($layer, $filters);
        }
    }

    /**
     * Process arguments.
     *
     * @param array $args The arguments to process.
     *
     * @return array The list of states/countries to process.
     *
     * @throws RuntimeException On error.
     */
    protected function expandArgs(array $args): array
    {
        if (empty($args) === true) {
            throw new RuntimeException('Must specify at least one region, state, or country.');
        }
        $list = [];
        $state = new State();
        foreach ($args as $arg) {
            if ($state->abbreviationExists($arg) === true) {
                $list[$state->getName($arg)] = [$arg];
            } elseif ($state->regionExists($arg) === true) {
                $list[$arg] = $state->getStatesInRegion($arg);
            } else {
                $list[$arg] = [$arg];
            }
        }
        ksort($list);
        return $list;
    }

    /**
     * Get the filtered set of records.
     *
     * @param array $filters The filters to apply.
     *
     * @return Generator<array> The filtered set of records.
     */
    protected function filtered(array $filters): Generator
    {
        foreach ($this->set->byName() as $record) {
            if (
                in_array($record['Brewery_Type__c'], self::SKIP_TYPES) === true ||
                preg_match('/Brewery In Planning/i', $record['Name']) === 1
            ) {
                continue;
            }
            foreach ($filters as $filter) {
                if (strlen($filter) === 2) {
                    if (
                        ($record['BillingAddress']['countryCode'] ?? null) === 'US' &&
                        ($record['BillingAddress']['stateCode'] ?? null) === $filter
                    ) {
                        yield $record;
                    }
                } else {
                    if (($record['BillingAddress']['country'] ?? null) === $filter) {
                        yield $record;
                    }
                }
            }
        }
    }

    /**
     * Get the storage object for this file.
     *
     * @param string $file The output file.
     *
     * @return KmlBuilder
     */
    protected function getStorageObject(string $file): KmlBuilder
    {
        return new KmlBuilder($file);
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
            Generate KML files from a set. If no set is specified, the latest
            will be used.
            EOS;
    }

    /**
     * Output a KML layer.
     *
     * @param string $layer The layer name to create.
     * @param array $filters The filters to apply to this layer.
     *
     * @return int The number of markers in this layer.
     *
     * @throws RuntimeException If a layer contains more than 2000 placemarks.
     */
    protected function layer(string $layer, array $filters): int
    {
        $this->store->startLayer($layer);
        printf("Creating layer: %s\n", $layer);
        $count = 0;
        foreach ($this->filtered($filters) as $record) {
            try {
                $this->store->placemark($record);
                ++$count;
            } catch (Exception $e) {
                printf("    %s\n", $e->getMessage());
            }
        }
        if ($count > self::LAYER_LIMIT) {
            throw new RuntimeException('Exceeded placemark layer limit.');
        }
        printf("    Layer contains %d placemarks\n", $count);
        $this->store->endLayer();
        return $count;
    }

}
