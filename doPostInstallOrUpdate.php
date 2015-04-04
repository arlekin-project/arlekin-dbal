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

    $arlekinCommonBinDir = sprintf(
        '%s%sbmichalski%sarlekin-core%sbin',
        $vendorsDir,
        DIRECTORY_SEPARATOR,
        DIRECTORY_SEPARATOR,
        DIRECTORY_SEPARATOR
    );

    $filenames = array(
        'arlekin-generate-coverage',
        'arlekin-generate-documentation',
        'arlekin-generate-markdown',
        'arlekin-phpcs'
    );

    foreach ($filenames as $filename) {
        $fileFullPath = sprintf(
            '%s%s%s',
            $arlekinCommonBinDir,
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

    $arlekinCommonPublishVendorsBinsFilePhp = sprintf(
        '%s/vendor/bmichalski/arlekin-core/publishVendorsBins.php',
        $currentDir
    );

    require $arlekinCommonPublishVendorsBinsFilePhp;

    $publishVendorsBins(
        sprintf(
            '%s%sbin',
            $currentDir,
            DIRECTORY_SEPARATOR
        ),
        $currentDir
    );
};