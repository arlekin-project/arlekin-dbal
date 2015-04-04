<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$postInstallOrUpdate = function (
    $currentDir
) {
    $vendorsDir = sprintf(
        '%s%svendor',
        $currentDir,
        DIRECTORY_SEPARATOR
    );

    $binDir = sprintf(
        '%s%sbin',
        $currentDir,
        DIRECTORY_SEPARATOR
    );

    $arlecchinoCommonBinDir = sprintf(
        '%s%sbmichalski%sarlecchino-core%sbin',
        $vendorsDir,
        DIRECTORY_SEPARATOR,
        DIRECTORY_SEPARATOR,
        DIRECTORY_SEPARATOR
    );

    $filenames = array(
        'arlecchino-generate-coverage',
        'arlecchino-generate-documentation',
        'arlecchino-generate-markdown',
        'arlecchino-phpcs'
    );

    foreach ($filenames as $filename) {
        $fileFullPath = sprintf(
            '%s%s%s',
            $arlecchinoCommonBinDir,
            DIRECTORY_SEPARATOR,
            $filename
        );

        $destinationFullPath = sprintf(
            '%s%s%s',
            $binDir,
            DIRECTORY_SEPARATOR,
            $filename
        );

        copy(
            $fileFullPath,
            $destinationFullPath
        );

        exec(
            sprintf(
                'chmod u+x %s',
                $destinationFullPath
            )
        );
    }

    $arlecchinoCommonPublishVendorsBinsFilePhp = sprintf(
        '%s/vendor/bmichalski/arlecchino-core/publishVendorsBins.php',
        $currentDir
    );

    require $arlecchinoCommonPublishVendorsBinsFilePhp;

    $publishVendorsBins(
        sprintf(
            '%s%sbin',
            $currentDir,
            DIRECTORY_SEPARATOR
        ),
        $currentDir
    );
};