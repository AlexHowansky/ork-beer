# Beer
Tools to generate a portable database of brewery information.

This is an updated version of my [beer](https://github.com/AlexHowansky/beer)
repository. A rewrite was required, as brewersassociation.org made significant
changes to their site and the old method of HTML scraping was (thankfully)
made irrelevant by the convenient addition of JSON-producing AJAX targets.

# What does this do?
There are two main goals of this project:
* Pull data from brewersassociation.org for every known brewery in the world,
  and save it to JSON files that are suitable for general use.
* Create maps of breweries.

# Why does this exist?
I was going on a road trip and wanted to visit some breweries. I located a
source of data, and created this code to get it into a format that I could
import into Google Maps. Also, because beer.

# Usage

Run `bin/beer` for basic usage. The following commands are available:

## update
Run `beer update` to download a fresh snapshot of the brewery database. This
is called a `set` and is named according to the current date, in `YYYYMMDD`
format. Sets are JSON files stored in the `data` directory.

## build
Run `beer build` to generate KMZ files for the predefined regions.

## sets
Run `beer sets` to see a list of sets that you have downloaded.

## info
Run `beer info <set>` to see some basic information about a set that you have
downloaded. If you do not provide a value for `<set>`, the most recent set will
be used.

## countries
Run `beer countries <set>` to see a unique list of countries in a set. If you
do not provide a value for `<set>`, the most recent set will be used.

## states
Run `beer states <set>` to see a unique list of states in a set. If you do not
provide a value for `<set>`, the most recent set will be used.

## regions
Run `beer regions` to see a list of the preferred regional maps and layers.
These are rougly based on US Census Bureau regions but are adjusted slightly so
that each region contains about 1000 breweries.

## kml
Run `beer kml <output file> [<set>] [<region|state|country> [<region|state|country>...]]`
to generate a KML file. If you do not provide a value for `<set>`, the most
recent set will be used. You must specify at least one state (by name or
abbreviation), region (by its name as shown in the `beer regions` command), or
country (by its name as shown in the `beer countries` command.) If you provide
multiple values, they will be output into the same file as separate layers. For
example:

`beer kml out NY`

Creates `out.kml` with one layer containing all the breweries in New York,
based on the most recent set.

`beer kml out 20191001 NY`

Creates `out.kml` with one layer containing all the breweries in New York,
based on the 20191001 set.

`beer kml out NY CT VT`

Creates `out.kml` with three layers, one for each state, based on the most
recent set.

`beer kml Belgium Belgium`

Creates `Belgium.kml` with one layer containing all the breweries in Belgium,
based on the most recent set.

Some records pulled from brewersassociation.org do not have LAT/LON coordinates.
To fill in the gaps where possible, provide an environment variable named
`GCP_API_KEY` which contains a Google Cloud Platform API key that has geocoding
rights.

## kmz
Use `kmz` as above to create a KMZ file instead of a KML file. This requires
PHP's `ext-zip` extension.

# Maps

If you just want to see the maps, you do not need to download this package. You
may simply use my existing maps, which are kept reasonably up-to-date and are
shared here:

* Northeast: http://bit.ly/beer-northeast
* Mid-Atlantic: https://bit.ly/beer-midatlantic
* Southeast: http://bit.ly/beer-southeast
* South: http://bit.ly/beer-south
* Midwest East: http://bit.ly/beer-midwest-east
* Midwest West: http://bit.ly/beer-midwest-west
* Mountain: http://bit.ly/beer-mountain
* West: http://bit.ly/beer-west
* California: http://bit.ly/beer-california
* Canada: http://bit.ly/beer-canada

Note that since Google Maps has a limit of 2000 points per, the data has been
split into groups roughly according to the US Census Bureau regions.

# Disclaimer

This is not my data, I am merely drawing maps. There will be errors. Please do
not report incorrect or missing information as a bug.
