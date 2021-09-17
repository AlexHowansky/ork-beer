<?php

/**
 * Ork Beer
 *
 * @package   Ork\Beer
 * @copyright 2019-2021 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/AlexHowansky/ork-beer/blob/master/LICENSE MIT License
 * @link      https://github.com/AlexHowansky/ork-beer
 */

namespace Ork\Beer\Command;

/**
 * Command to print some information about a set.
 */
class Info extends AbstractCommand
{

    /**
     * Run the command.
     *
     * @param array $args The arguments passed to the command, if any.
     *
     * @return void
     */
    public function __invoke(array $args = []): void
    {
        $set = new \Ork\Beer\Set(array_shift($args));
        printf(
            "Set %s contains %s breweries from %d countries.\n",
            $set->getName(),
            number_format(count($set)),
            number_format(count($set->getCountries()))
        );
    }

    /**
     * Output the help text for this command.
     *
     * @return string The help text for this command.
     */
    public function help(): string
    {
        return <<<EOS
            [<set>]
            Print some statistics about a set. If no set is specified, the
            latest will be used.
            EOS;
    }

}
