<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Tests\Manager;

use Arlekin\DatabaseAbstractionLayer\DatabaseConnectionInterface;
use Arlekin\DatabaseAbstractionLayer\DriverInterface;
use Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager;
use Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManagerInterface;
use PHPUnit_Framework_TestCase;

class DatabaseConnectionManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DatabaseConnectionManager
     */
    protected $databaseConnectionManager;

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::__construct
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
                'default' => [
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
     * @covers Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::getConnectionWithName
     * @covers Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::instanciateDatabaseConnection
     * @covers Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::instanciateNamedDatabaseConnection
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
     * @covers Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::getConnectionWithName
     * @covers Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::instanciateDatabaseConnection
     * @covers Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::instanciateNamedDatabaseConnection
     */
    public function testGetConnectionWithNameTwice()
    {
        $databaseConnectionManager = $this->doTestGetConnectionWithNameGetDatabaseConnectionManager();

        $firstOne = $databaseConnectionManager->getConnectionWithName('default');
        $secondOne = $databaseConnectionManager->getConnectionWithName('default');

        $this->assertSame($firstOne, $secondOne);
    }
}
