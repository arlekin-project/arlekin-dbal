<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Tests;

use Arlekin\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Arlekin\Dbal\Driver\Pdo\MySql\Driver;

class DriverTest extends AbstractBasePdoMySqlTest
{
    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Driver::instanciateDatabaseConnection
     */
    public function testInstanciateDatabaseConnection()
    {
        $driver = $this->getDriver();

        $connection = $driver->instanciateDatabaseConnection(
            [
                'host' => 'test_host',
                'port' => 4242,
                'database' => 'test_database',
                'user' => 'test_user',
                'password' => 'test_password',
            ]
        );

        $this->assertInstanceOf(DatabaseConnection::class, $connection);

        $this->assertAttributeSame('test_host', 'host', $connection);
        $this->assertAttributeSame(4242, 'port', $connection);
        $this->assertAttributeSame('test_database', 'database', $connection);
        $this->assertAttributeSame('test_user', 'user', $connection);
        $this->assertAttributeSame('test_password', 'password', $connection);
    }

    /**
     * @return Driver
     */
    protected function getDriver()
    {
        return new Driver();
    }
}
