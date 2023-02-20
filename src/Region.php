<?php

/**
 * Ork Beer
 *
 * @package   Ork\Beer
 * @copyright 2019-2023 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/AlexHowansky/ork-beer/blob/master/LICENSE MIT License
 * @link      https://github.com/AlexHowansky/ork-beer
 */

namespace Ork\Beer;

use RuntimeException;

/**
 * Map region breakdown. These are roughy based on US Census Bureau regions but
 * are modified slightly to accomodate the placemark limit.
 */
class Region
{

    /**
     * The first array level is the map, the second array level is the layer.
     */
    protected const REGIONS = [
        'California' => [
            'California' => ['CA'],
        ],
        'Canada' => [
            'Canada' => ['Canada'],
        ],
        'Mid-Atlantic' => [
            'Washington DC' => ['DC'],
            'Delaware' => ['DE'],
            'Maryland' => ['MD'],
            'New Jersey' => ['NJ'],
            'Pennsylvania' => ['PA'],
            'Virginia' => ['VA'],
            'West Virginia' => ['WV'],
        ],
        'Midwest East' => [
            'Illinois' => ['IL'],
            'Indiana' => ['IN'],
            'Ohio' => ['OH'],
            'Michigan' => ['MI'],
            'Wisconsin' => ['WI'],
        ],
        'Midwest West' => [
            'Iowa' => ['IA'],
            'Kansas' => ['KS'],
            'Minnesota' => ['MN'],
            'Missouri' => ['MO'],
            'Nebraska' => ['NE'],
            'North Dakota' => ['ND'],
            'South Dakota' => ['SD'],
        ],
        'Mountain' => [
            'Arizona' => ['AZ'],
            'Colorado' => ['CO'],
            'Idaho' => ['ID'],
            'Montana' => ['MT'],
            'Nevada' => ['NV'],
            'New Mexico' => ['NM'],
            'Utah' => ['UT'],
            'Wyoming' => ['WY'],
        ],
        'Northeast' => [
            'Connecticut' => ['CT'],
            'Maine' => ['ME'],
            'Massachusetts' => ['MA'],
            'New Hampshire' => ['NH'],
            'New York' => ['NY'],
            'Rhode Island' => ['RI'],
            'Vermont' => ['VT'],
        ],
        'South' => [
            'Alabama' => ['AL'],
            'Arkansas' => ['AR'],
            'Kentucky' => ['KY'],
            'Louisiana' => ['LA'],
            'Mississippi' => ['MS'],
            'Oklahoma' => ['OK'],
            'Tennesee' => ['TN'],
            'Texas' => ['TX'],
        ],
        'Southeast' => [
            'Florida' => ['FL'],
            'Georgia' => ['GA'],
            'North Carolina' => ['NC'],
            'South Carolina' => ['SC'],
        ],
        'West' => [
            'Alaska' => ['AK'],
            'Oregon' => ['OR'],
            'Washington' => ['WA'],
            'Hawaii' => ['HI'],
        ],
    ];

    /**
     * Get the layers in a region.
     *
     * @param string $name The region to get the layers for.
     *
     * @return array The layers in the region.
     *
     * @throws RuntimeException If the region doesn't exist.
     */
    public function getLayersInRegion(string $name): array
    {
        return self::REGIONS[$name]
            ?? throw new RuntimeException('No such region: ' . $name);
    }

    /**
     * Get the regions.
     *
     * @return array The regions.
     */
    public function getRegions(): array
    {
        return array_keys(self::REGIONS);
    }

    /**
     * Determine if a region exists.
     *
     * @param string $name The region to check for.
     *
     * @return bool True if the region exists.
     */
    public function nameExists(string $name): bool
    {
        return array_key_exists($name, self::REGIONS);
    }

}
