<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Migration\Manager;

interface MigrationManagerInterface
{
    /**
     * @param string $version
     *
     * @return boolean true if version has already been applied,
     * false otherwise
     */
    public function versionApplied($version);

    /**
     * @param string $migrationsFolderFullPath
     *
     * @return array
     */
    public function migrate($migrationsFolderFullPath);
}
