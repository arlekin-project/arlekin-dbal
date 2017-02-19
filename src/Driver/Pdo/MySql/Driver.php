<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql;

use Calam\Dbal\DriverInterface;

class Driver implements DriverInterface
{
    /**
     * {@inheritdoc}
     */
    public function instanciateDatabaseConnection(array $parameters)
    {
        if (isset($parameters['logger'])) {
            return new LoggedDatabaseConnection(
                $parameters['host'],
                $parameters['port'],
                $parameters['database'],
                $parameters['user'],
                $parameters['password'],
                $parameters['logger']
            );
        } else {
            return new DatabaseConnection(
                $parameters['host'],
                $parameters['port'],
                $parameters['database'],
                $parameters['user'],
                $parameters['password']
            );
        }
    }
}
