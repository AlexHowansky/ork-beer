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
 * State utility methods.
 */
class State
{

    /**
     * The state name / abbreviation map.
     */
    protected const STATES = [
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
     * Does an abbreviation exist?
     *
     * @param string $abbr The abbreviation to check.
     *
     * @return bool True if the abbreviation exists.
     */
    public function abbreviationExists(string $abbr): bool
    {
        return array_key_exists(strtoupper($abbr), self::STATES);
    }

    /**
     * Get the abbreviation of a state.
     *
     * @param string $name The state name.
     *
     * @return string The state abbreviation.
     *
     * @throws RuntimeException On error.
     */
    public function getAbbreviationForName(string $name): string
    {
        return array_flip(self::STATES)[ucwords(strtolower($name))]
            ?? throw new RuntimeException('No such state: ' . $name);
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
    public function getNameForAbbreviation(string $abbr): string
    {
        return self::STATES[strtoupper($abbr)]
            ?? throw new RuntimeException('No such state: ' . $abbr);
    }

    /**
     * Does a name exist?
     *
     * @param string $name The name to check.
     *
     * @return bool True if the name exists.
     */
    public function nameExists(string $name): bool
    {
        return array_search(ucwords(strtolower($name)), self::STATES) !== false;
    }

}
