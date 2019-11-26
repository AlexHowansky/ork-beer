<?php

/**
 * Ork Beer
 *
 * @package   Ork\Beer
 * @copyright 2019 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/AlexHowansky/ork-beer/blob/master/LICENSE MIT License
 * @link      https://github.com/AlexHowansky/ork-beer
 */

namespace Ork\Beer;

/**
 * KML generation class.
 */
class Kml
{

    /**
     * Use this type if none is explicitly specified.
     */
    const DEFAULT_TYPE = 'other';

    /**
     * The icon to use for placemarks.
     */
    const ICON = '1879-stein-beer_4x.png';

    /**
     * The icon base URL.
     */
    const ICON_BASE = 'https://mt.google.com/vt/icon/name=icons/onion/SHARED-mymaps-container_4x.png,icons/onion/';

    /**
     * The icon scale.
     */
    const ICON_SCALE = '1.0';

    /**
     * The color to use for each type.
     */
    const TYPE_COLORS = [
        'brewpub' => 'F8971B',
        'contract' => 'F4EB37',
        'large' => '7C3592',
        'micro' => '34E5EB',
        'other' => '000000',
        'planning' => 'E0E0E0',
        'proprietor' => '4186F0',
        'regional' => '009D57',
        'taproom' => 'D04040',
    ];

    /**
     * The output file name.
     *
     * @var string $file
     */
    protected string $file;

    /**
     * The XMLWriter object we'll build the KML file with.
     *
     * @var \XMLWriter $kml
     */
    protected \XMLWriter $kml;

    /**
     * Constructor.
     *
     * @param string $file The output file name.
     */
    public function __construct(string $file)
    {
        $this->file = basename($file, '.kml');
        $this->kml = new \XMLWriter();
        $this->kml->openURI($this->file . '.kml');
        $this->kml->startDocument('1.0', 'UTF-8');
        $this->kml->setIndent(true);
        $this->kml->setIndentString('    ');
        $this->kml->startElement('kml');
        $this->kml->writeAttribute('xmlns', 'http://www.opengis.net/kml/2.2');
        $this->kml->startElement('Document');
    }

    /**
     * Finish up.
     */
    public function __destruct()
    {
        foreach (self::TYPE_COLORS as $type => $color) {
            $this->kml->startElement('Style');
            $this->kml->writeAttribute('id', $type);
            $this->kml->startElement('IconStyle');
            $this->kml->startElement('Icon');
            $this->kml->writeElement(
                'href',
                self::ICON_BASE . self::ICON . '&highlight=' . $color . '&scale=' . self::ICON_SCALE
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
     * @return Kml
     */
    public function endLayer(): Kml
    {
        $this->kml->endElement();
        return $this;
    }

    /**
     * Create the <ExtendedData> section.
     *
     * @param array $data The data to create XML for.
     *
     * @return Kml
     */
    protected function extendedData(array $data): Kml
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
     * @return Kml
     *
     * @throws \RuntimeException On error.
     */
    public function placemark(array $row): Kml
    {
        if (
            empty($row['Longitude']) === true ||
            empty($row['Latitude']) === true ||
            ($row['Longitude'] > -0.1 && $row['Longitude'] < 0.1) ||
            ($row['Latitude'] > -0.1 && $row['Latitude'] < 0.1)
        ) {
            throw new \RuntimeException('No lat/lon available for brewery: ' . $row['InstituteName']);
        }

        $this->kml->startElement('Placemark');
        $this->kml->writeElement('name', $row['InstituteName']);
        $this->kml->writeElement(
            'styleUrl',
            '#' . (
                array_key_exists($row['BreweryType'], self::TYPE_COLORS) === true
                    ? $row['BreweryType']
                    : self::DEFAULT_TYPE
            )
        );
        $this->kml->startElement('Point');
        $this->kml->writeElement('coordinates', sprintf('%s,%s', $row['Longitude'], $row['Latitude']));
        $this->kml->endElement();

        $this->extendedData(
            array_filter([
                'Type' => $row['BreweryType'],
                'Website' => $row['WebSite'],
                'Open To Public' => empty($row['NotOpentoPublic']) === true ? 'Yes' : 'No',
                'Phone' => $row['WorkPhone'],
                'Address' => $row['Address1'],
                'City' => $row['City'],
                'State' => $row['StateProvince'],
                'Zip' => $row['Zip'],
                'Founded' => $row['FoundedDate'] ? date('F jS, Y', strtotime($row['FoundedDate'])) : '',
                'Parent' => $row['TopParentCoName'] === $row['InstituteName'] ? '' : $row['TopParentCoName'],
            ])
        );

        $this->kml->endElement();

        return $this;
    }

    /**
     * Start a layer.
     *
     * @param string $layer The layer name.
     *
     * @return Kml
     */
    public function startLayer(string $layer): Kml
    {
        $this->kml->startElement('Folder');
        $this->kml->writeElement('name', $layer);
        return $this;
    }

}
