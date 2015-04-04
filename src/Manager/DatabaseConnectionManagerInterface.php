<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\Manager;

use Arlecchino\DatabaseAbstractionLayer\DatabaseConnectionInterface;

interface DatabaseConnectionManagerInterface
{
    /**
     * @param array $parameters
     *
     * @return DatabaseConnectionInterface
     */
    public function instanciateDatabaseConnection(
        array $parameters
    );

    /**
     * @param string $name
     *
     * @return DatabaseConnectionInterface
     */
    public function instanciateNamedDatabaseConnection(
        $name
    );
}
