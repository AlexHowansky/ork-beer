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
 * State map.
 */
class State
{

    /**
     * The list of state names.
     */
    protected const NAMES = [
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'AS' => 'American Samoa',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'DC' => 'District of Columbia',
        'FL' => 'Florida',
        'FM' => 'Federated States of Micronesia',
        'GA' => 'Georgia',
        'GU' => 'Guam',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MH' => 'Marshall Islands',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MP' => 'Northern Mariana Islands',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'PR' => 'Puerto Rico',
        'PW' => 'Palau',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'VI' => 'Virgin Islands',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
    ];

    /**
     * The states composing each region.
     *
     * These regions are defined by the US Census Bureau.
     */
    protected const REGIONS = [
        // "Northeast" region.
        'Mid Atlantic' => ['NJ', 'NY', 'PA'],
        'New England' => ['CT', 'MA', 'ME', 'NH', 'RI', 'VT'],

        // "South" region.
        'East South Central' => ['AL', 'KY', 'MS', 'TN'],
        'South Atlantic' => ['DC', 'DE', 'FL', 'GA', 'NC', 'SC', 'VA', 'WV'],
        'West South Central' => ['AR', 'LA', 'OK', 'TX'],

        // "West" region.
        'California' => ['CA'],
        'Mountain' => ['AZ', 'CO', 'ID', 'MT', 'NM', 'NV', 'UT', 'WY'],
        'Pacific' => ['AK', 'HI', 'OR', 'WA'],

        // "Midwest" region.
        'East North Central' => ['IL', 'IN', 'OH', 'MI', 'WI'],
        'West North Central' => ['IA', 'KS', 'MN', 'MO', 'ND', 'NE', 'SD'],
    ];

    /**
     * Does an abbreviation exist?
     *
     * @param string $abbr The abbreviation to check.
     *
     * @return bool True if the abbreviation exists.
     */
    public function abbreviationExists(string $abbr): bool
    {
        return array_key_exists($abbr, self::NAMES);
    }

    /**
     * Get the name of a state.
     *
     * @param string $abbr The state abbreviation.
     *
     * @return string The state name.
     *
     * @throws RuntimeException On error.
     */
    public function getName(string $abbr): string
    {
        if ($this->abbreviationExists($abbr) === false) {
            throw new RuntimeException('No such state: ' . $abbr);
        }
        return self::NAMES[$abbr];
    }

    /**
     * Get the list of state abbreviations.
     *
     * @return array The list of state abbreviations.
     */
    public function getStateAbbreviations(): array
    {
        return array_keys(self::NAMES);
    }

    /**
     * Get the states in a region.
     *
     * @param string $region The region.
     *
     * @return array The states in the region.
     *
     * @throws RuntimeException On error.
     */
    public function getStatesInRegion(string $region): array
    {
        if ($this->regionExists($region) === false) {
            throw new RuntimeException('No such region: ' . $region);
        }
        return self::REGIONS[$region];
    }

    /**
     * Does a region exist?
     *
     * @param string $region The region to check.
     *
     * @return bool True if the region exists.
     */
    public function regionExists(string $region): bool
    {
        return array_key_exists($region, self::REGIONS);
    }

}
