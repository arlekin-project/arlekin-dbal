<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */

if (!isset($GLOBALS['arlekin_bootstrap_loaded'])
    || !$GLOBALS['arlekin_bootstrap_loaded']) {
    $currentDirectory = __DIR__;

    $GLOBALS['arlekin_bootstrap_loaded'] = true;

    error_reporting(E_ALL | E_STRICT);

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');

    $autoloadFilePhp = sprintf(
        '%s/../vendor/autoload.php',
        $currentDirectory
    );

    require $autoloadFilePhp;
}
