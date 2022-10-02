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

use Exception;
use Ork\Beer\KmlBuilder;
use Ork\Beer\Set;
use Ork\Beer\State;
use RuntimeException;

/**
 * Command to output KML files.
 */
class Kml extends AbstractCommand
{

    protected const SKIP_TYPES = ['Brewery In Planning', 'Office only location'];

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

        $set = new Set(preg_match('/^\d{8}$/', $args[0] ?? null) === 1 ? array_shift($args) : null);
        printf("Using set: %s\n", $set->getName());

        $args = $this->expandArgs($args);
        $store = $this->getStorageObject($output);
        foreach ($args as $layer => $filters) {
            $store->startLayer($layer);
            printf("Creating layer: %s\n", $layer);
            $markerCount = 0;
            foreach ($set->byName() as $record) {
                if (
                    in_array($record['Brewery_Type__c'], self::SKIP_TYPES) === true ||
                    preg_match('/Brewery In Planning/i', $record['Name'])
                ) {
                    continue;
                }
                if (
                    in_array(
                        strlen($filters[0]) === 2
                            ? ($record['BillingAddress']['stateCode'] ?? null)
                            : ($record['BillingAddress']['country'] ?? null),
                        $filters
                    ) === false
                ) {
                    continue;
                }
                try {
                    $store->placemark($record);
                    ++$markerCount;
                } catch (Exception $e) {
                    printf("    %s\n", $e->getMessage());
                }
            }
            $store->endLayer();
            printf("    Layer contains %d placemarks\n", $markerCount);
            if ($markerCount > 2000) {
                throw new RuntimeException('Exceeded placemark layer limit.');
            }
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

}
