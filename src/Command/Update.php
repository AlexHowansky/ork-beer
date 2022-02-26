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

/**
 * Command to get the latest data.
 */
class Update extends AbstractCommand
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
        $file = new \Ork\Beer\File();
        printf("Downloaded %s breweries to set %s.\n", number_format($file->update()), $file->getLatestSet());
    }

    /**
     * Output the help text for this command.
     *
     * @return string The help text for this command.
     */
    public function help(): string
    {
        return 'Download the latest data and save it locally.';
    }

}
