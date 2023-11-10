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

use Ork\Beer\Set;
use Ork\Csv\Writer;

/**
 * Command to convert the data to flat CSV.
 */
class Csv extends AbstractCommand
{
    // How to map their JSON packet to a flat CSV.
    protected const MAP = [
        'id' => 'Id',
        'name' => 'Name',
        'breweryType' => 'Brewery_Type__c',
        'phone' => 'Phone',
        'website' => 'Website',
        'isCraft' => 'Is_Craft_Brewery__c',
        'street' => ['BillingAddress', 'street'],
        'city' => ['BillingAddress', 'city'],
        'state' => ['BillingAddress', 'state'],
        'stateCode' => ['BillingAddress', 'stateCode'],
        'postalCode' => ['BillingAddress', 'postalCode'],
        'country' => ['BillingAddress', 'country'],
        'countryCode' => ['BillingAddress', 'countryCode'],
        'latitude' => ['BillingAddress', 'latitude'],
        'longitude' => ['BillingAddress', 'longitude'],
        'parentId' => ['Parent', 'Id'],
    ];

    /**
     * Run the command.
     *
     * @param array $args The arguments passed to the command, if any.
     *
     * @return void
     */
    public function __invoke(array $args = []): void
    {
        $csv = new Writer();
        foreach (new Set(array_shift($args)) as $item) {
            $row = [];
            foreach (self::MAP as $us => $them) {
                $row[$us] = is_array($them) === true ? ($item[$them[0]][$them[1]] ?? '') : ($item[$them] ?? '');
            }
            $csv->write($row);
        }
    }

    public function help(): string
    {
        return <<<EOS
            [<set>]
            Export the set to CSV. If no set is specified, the latest will be
            used.
            EOS;
    }

}
