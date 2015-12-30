<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql;

use Arlekin\Dbal\DriverInterface;

class Driver implements DriverInterface
{
    /**
     * {@inheritdoc}
     */
    public function instanciateDatabaseConnection(array $parameters)
    {
        return new DatabaseConnection(
            $parameters['host'],
            $parameters['port'],
            $parameters['database'],
            $parameters['user'],
            $parameters['password']
        );
    }
}
