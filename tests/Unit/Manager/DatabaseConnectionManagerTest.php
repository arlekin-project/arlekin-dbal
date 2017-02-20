<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Manager;

use Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Calam\Dbal\DriverInterface;
use Calam\Dbal\Manager\DatabaseConnectionManager;
use Calam\Dbal\Manager\DatabaseConnectionManagerInterface;
use Calam\Dbal\Tests\BaseTest;

class DatabaseConnectionManagerTest extends BaseTest
{
    /**
     * @var DatabaseConnectionManager
     */
    protected $databaseConnectionManager;

    /**
     * @covers Calam\Dbal\Manager\DatabaseConnectionManager::__construct
     */
    public function testConstruct()
    {
        $config = [
            'foo',
        ];
        
        $drivers = [
            'bar',
        ];
        
        $instance = new DatabaseConnectionManager($config, $drivers);
        
        $this->assertAttributeInternalType('array', 'configuration', $instance);
        $this->assertAttributeInternalType('array', 'driversByName', $instance);
        $this->assertAttributeInternalType('array', 'connectionsByName', $instance);
        
        $this->assertAttributeSame($config, 'configuration', $instance);
        $this->assertAttributeSame($drivers, 'driversByName', $instance);
    }

    /**
     * @return DatabaseConnectionManagerInterface
     */
    protected function doTestGetConnectionWithNameGetDatabaseConnectionManager()
    {
        $databaseConnectionMock = $this->createMock(DatabaseConnection::class);

        $driverMock = $this->createMock(DriverInterface::class);

        $driverMock->method(
            'instanciateDatabaseConnection'
        )->will(
            $this->returnValue(
                $databaseConnectionMock
            )
        );
        
        $config = [
            'connections' => [
                [
                    'name' => 'default',
                    'driver' => 'foobar',
                ],
            ],
        ];
        
        $drivers = [
            'foobar' => $driverMock,
        ];

        return new DatabaseConnectionManager($config, $drivers);
    }

    /**
     * @covers Calam\Dbal\Manager\DatabaseConnectionManager::getConnectionWithName
     * @covers Calam\Dbal\Manager\DatabaseConnectionManager::instanciateDatabaseConnection
     * @covers Calam\Dbal\Manager\DatabaseConnectionManager::instanciateNamedDatabaseConnection
     */
    public function testGetConnectionWithName()
    {
        $databaseConnectionManager = $this->doTestGetConnectionWithNameGetDatabaseConnectionManager();

        $instance = $databaseConnectionManager->getConnectionWithName('default');

        $this->assertInstanceOf(
            DatabaseConnection::class,
            $instance
        );
    }

    /**
     * @covers Calam\Dbal\Manager\DatabaseConnectionManager::getConnectionWithName
     * @covers Calam\Dbal\Manager\DatabaseConnectionManager::instanciateDatabaseConnection
     * @covers Calam\Dbal\Manager\DatabaseConnectionManager::instanciateNamedDatabaseConnection
     */
    public function testGetConnectionWithNameTwice()
    {
        $databaseConnectionManager = $this->doTestGetConnectionWithNameGetDatabaseConnectionManager();

        $firstOne = $databaseConnectionManager->getConnectionWithName('default');
        $secondOne = $databaseConnectionManager->getConnectionWithName('default');

        $this->assertSame($firstOne, $secondOne);
    }
}
