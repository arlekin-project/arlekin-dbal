#!/usr/bin/env php
<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */

$currentDirectory = __DIR__;

$doInstallOrUpdateFilePhp = sprintf(
    '%s/doPostInstallOrUpdate.php',
    $currentDirectory
);

require $doInstallOrUpdateFilePhp;

$postInstallOrUpdate(
    $currentDirectory
);
