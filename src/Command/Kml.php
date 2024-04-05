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
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Query\GeocodeQuery;
use Geocoder\StatefulGeocoder;
use GuzzleHttp\Client;
use Ork\Beer\KmlBuilder;
use Ork\Beer\Region;
use Ork\Beer\Set;
use Ork\Beer\State;
use RuntimeException;

/**
 * Command to output KML files.
 */
class Kml extends AbstractCommand
{

    // Google maps does not allow more than this many placemarks on a single map, regardless of layers.
    protected const PLACEMARK_LIMIT = 2000;

    // Don't include placemarks for these brewery types.
    protected const SKIP_TYPES = ['Brewery In Planning', 'NonBeer', 'Office only location'];

    protected StatefulGeocoder $geocoder;

    protected Set $set;

    protected KmlBuilder $store;

    public function __construct()
    {
        $key = $_ENV['GCP_API_KEY'] ?? $_SERVER['GCP_API_KEY'] ?? null;
        if (empty($key) === false) {
            $this->geocoder = new StatefulGeocoder(new GoogleMaps(new Client(), null, $key), 'en');
        }
    }

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
        $count = 0;
        foreach ($this->expandArgs($args) as $layer => $filters) {
            $count += $this->layer($layer, $filters);
        }
        printf("KML contains %d placemarks\n", $count);
        if ($count > self::PLACEMARK_LIMIT) {
            printf("WARNING Exceeded placemark limit of %s\n", self::PLACEMARK_LIMIT);
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
        $region = new Region();
        foreach ($args as $arg) {
            if ($state->abbreviationExists($arg) === true) {
                $list[$state->getNameForAbbreviation($arg)] = [strtoupper($arg)];
            } elseif ($state->nameExists($arg) === true) {
                $list[ucwords(strtolower($arg))] = [$state->getAbbreviationForName($arg)];
            } elseif ($region->nameExists($arg) === true) {
                $list = array_merge($list, $region->getLayersInRegion($arg));
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
                preg_match('/Brewery In Planning/i', $record['Name']) === 1 ||
                preg_match('/ Household$/', $record['Name']) === 1
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

    protected function geocode(array $record): array
    {
        if (
            empty($record['BillingAddress']['latitude']) === false &&
            empty($record['BillingAddress']['longitude']) === false
        ) {
            return $record;
        }

        if (empty($record['BillingAddress']['street']) === true) {
            throw new RuntimeException('No lat/lon or address available for brewery: ' . $record['Name']);
        }

        if (isset($this->geocoder) === false) {
            throw new RuntimeException('No geocoder available for brewery: ' . $record['Name']);
        }

        $address = sprintf(
            '%s, %s, %s %s',
            $record['BillingAddress']['street'],
            $record['BillingAddress']['city'],
            $record['BillingAddress']['stateCode'],
            $record['BillingAddress']['postalCode']
        );
        $result = $this->geocoder->geocodeQuery(GeocodeQuery::create($address));
        if (count($result) === 0) {
            printf("    WARNING Failed geocoding brewery: %s\n", $record['Name']);
        } else {
            $record['BillingAddress']['latitude'] = $result->first()->getCoordinates()?->getLatitude();
            $record['BillingAddress']['longitude'] = $result->first()->getCoordinates()?->getLongitude();
            printf(
                "    NOTICE Succeeded geocoding brewery: %s [%s, %s]\n",
                $record['Name'],
                $record['BillingAddress']['latitude'],
                $record['BillingAddress']['longitude']
            );
        }

        return $record;
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
                $this->store->placemark($this->geocode($record));
                ++$count;
            } catch (Exception $e) {
                printf("    WARNING %s\n", $e->getMessage());
            }
        }
        printf("    Layer contains %d placemarks\n", $count);
        $this->store->endLayer();
        return $count;
    }

}
