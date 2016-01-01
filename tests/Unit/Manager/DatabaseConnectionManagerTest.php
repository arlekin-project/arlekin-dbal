<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Unit\Manager;

use Arlekin\Dbal\DatabaseConnectionInterface;
use Arlekin\Dbal\DriverInterface;
use Arlekin\Dbal\Manager\DatabaseConnectionManager;
use Arlekin\Dbal\Manager\DatabaseConnectionManagerInterface;
use Arlekin\Dbal\Tests\AbstractBaseTest;

class DatabaseConnectionManagerTest extends AbstractBaseTest
{
    /**
     * @var DatabaseConnectionManager
     */
    protected $databaseConnectionManager;

    /**
     * @covers Arlekin\Dbal\Manager\DatabaseConnectionManager::__construct
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
        $databaseConnectionMock = $this->getMock(
            DatabaseConnectionInterface::class
        );

        $driverMock = $this->getMock(
            DriverInterface::class
        );

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
     * @covers Arlekin\Dbal\Manager\DatabaseConnectionManager::getConnectionWithName
     * @covers Arlekin\Dbal\Manager\DatabaseConnectionManager::instanciateDatabaseConnection
     * @covers Arlekin\Dbal\Manager\DatabaseConnectionManager::instanciateNamedDatabaseConnection
     */
    public function testGetConnectionWithName()
    {
        $databaseConnectionManager = $this->doTestGetConnectionWithNameGetDatabaseConnectionManager();

        $instance = $databaseConnectionManager->getConnectionWithName('default');

        $this->assertInstanceOf(
            DatabaseConnectionInterface::class,
            $instance
        );
    }

    /**
     * @covers Arlekin\Dbal\Manager\DatabaseConnectionManager::getConnectionWithName
     * @covers Arlekin\Dbal\Manager\DatabaseConnectionManager::instanciateDatabaseConnection
     * @covers Arlekin\Dbal\Manager\DatabaseConnectionManager::instanciateNamedDatabaseConnection
     */
    public function testGetConnectionWithNameTwice()
    {
        $databaseConnectionManager = $this->doTestGetConnectionWithNameGetDatabaseConnectionManager();

        $firstOne = $databaseConnectionManager->getConnectionWithName('default');
        $secondOne = $databaseConnectionManager->getConnectionWithName('default');

        $this->assertSame($firstOne, $secondOne);
    }
}
