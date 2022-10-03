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

use RuntimeException;
use XMLWriter;

/**
 * KML generation class.
 */
class KmlBuilder
{

    /**
     * Use this type if none is explicitly specified.
     */
    protected const DEFAULT_TYPE = 'Other';

    /**
     * The icon to use for placemarks.
     */
    protected const ICON = '1879-stein-beer_4x.png';

    /**
     * The icon base URL.
     */
    protected const ICON_BASE =
        'https://mt.google.com/vt/icon/name=icons/onion/SHARED-mymaps-container_4x.png,icons/onion/';

    /**
     * The icon scale.
     */
    protected const ICON_SCALE = '1.0';

    /**
     * The color to use for each type.
     */
    protected const TYPE_COLORS = [
        'Alt Prop' => '4186F0',
        'Brewpub' => 'F8971B',
        'Contract' => 'F4EB37',
        'Large' => '7C3592',
        'Location' => 'E0E0E0',
        'Micro' => '34E5EB',
        'NonBeer' => '7cf72f',
        'Other' => '000000',
        'Regional' => '009D57',
        'Taproom' => 'D04040',
    ];

    /**
     * The output file name.
     */
    protected string $file;

    /**
     * The XMLWriter object we'll build the KML file with.
     */
    protected XMLWriter $kml;

    /**
     * Constructor.
     *
     * @param string $file The output file name.
     */
    public function __construct(string $file)
    {
        $this->file = basename($file, '.kml');
        $this->kml = new XMLWriter();
        $this->kml->openURI($this->file . '.kml');
        $this->kml->startDocument('1.0', 'UTF-8');
        $this->kml->setIndent(true);
        $this->kml->setIndentString('    ');
        $this->kml->startElement('kml');
        $this->kml->writeAttribute('xmlns', 'http://www.opengis.net/kml/2.2');
        $this->kml->startElement('Document');
    }

    public function __destruct()
    {
        foreach (self::TYPE_COLORS as $type => $color) {
            $this->kml->startElement('Style');
            $this->kml->writeAttribute('id', $type);
            $this->kml->startElement('IconStyle');
            $this->kml->startElement('Icon');
            $this->kml->writeElement(
                'href',
                self::ICON_BASE . self::ICON . '?' . http_build_query([
                    'highlight' => $color,
                    'scale' => self::ICON_SCALE,
                ])
            );
            $this->kml->endElement();
            $this->kml->endElement();
            $this->kml->startElement('LabelStyle');
            $this->kml->writeElement('scale', '1.0');
            $this->kml->endElement();
            $this->kml->endElement();
        }
        $this->kml->endElement();
        $this->kml->endElement();
        $this->kml->endDocument();
        $this->kml->flush();
    }

    /**
     * End a layer.
     *
     * @return KmlBuilder Allow method chaining.
     */
    public function endLayer(): KmlBuilder
    {
        $this->kml->endElement();
        return $this;
    }

    /**
     * Create the <ExtendedData> section.
     *
     * @param array $data The data to create XML for.
     *
     * @return KmlBuilder Allow method chaining.
     */
    protected function extendedData(array $data): KmlBuilder
    {
        $this->kml->startElement('ExtendedData');
        foreach ($data as $name => $value) {
            if (empty($value) === false) {
                $this->kml->startElement('Data');
                $this->kml->writeAttribute('name', $name);
                $this->kml->writeElement('value', $value);
                $this->kml->endElement();
            }
        }
        $this->kml->endElement();
        return $this;
    }

    /**
     * Create a placemark section.
     *
     * @param array $row The data row to create a placemark for.
     *
     * @return KmlBuilder Allow method chaining.
     *
     * @throws RuntimeException If no lat/lon is available.
     */
    public function placemark(array $row): KmlBuilder
    {
        if (
            empty($row['BillingAddress']['longitude']) === true ||
            empty($row['BillingAddress']['latitude']) === true
        ) {
            throw new RuntimeException('No lat/lon available for brewery: ' . $row['Name']);
        }

        $this->kml->startElement('Placemark');
        $this->kml->writeElement('name', $row['Name']);
        $this->kml->writeElement(
            'styleUrl',
            sprintf(
                '#%s',
                array_key_exists($row['Brewery_Type__c'], self::TYPE_COLORS) === true
                    ? $row['Brewery_Type__c']
                    : self::DEFAULT_TYPE
            )
        );
        $this->kml->startElement('Point');
        $this->kml->writeElement(
            'coordinates',
            sprintf('%s,%s', $row['BillingAddress']['longitude'], $row['BillingAddress']['latitude'])
        );
        $this->kml->endElement();

        $this->extendedData([
            'Type' => $row['Brewery_Type__c'],
            'Website' => $row['Website'],
            'Phone' => $row['Phone'],
            'Address' => $row['BillingAddress']['street'],
            'City' => $row['BillingAddress']['city'],
            'State' => $row['BillingAddress']['stateCode'],
            'Zip' => $row['BillingAddress']['postalCode'],
            'Craft' => (bool) $row['Is_Craft_Brewery__c'] === true ? 'yes' : 'no',
            'Parent' => $row['Parent'][0]['Name'] ?? null,
        ]);

        $this->kml->endElement();

        return $this;
    }

    /**
     * Start a layer.
     *
     * @param string $layer The layer name.
     *
     * @return KmlBuilder Allow method chaining.
     */
    public function startLayer(string $layer): KmlBuilder
    {
        $this->kml->startElement('Folder');
        $this->kml->writeElement('name', $layer);
        return $this;
    }

}
