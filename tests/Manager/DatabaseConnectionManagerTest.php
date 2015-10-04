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
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        $containerMock = $this->getMock(
            ContainerInterface::class
        );

        $instance = new DatabaseConnectionManager($containerMock);

        $this->assertAttributeInstanceOf(
            ContainerInterface::class,
            'container',
            $instance
        );
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

        $containerMock = $this->getMock(
            ContainerInterface::class
        );

        $containerMock->method(
            'getParameter'
        )->will(
            $this->returnCallback(
                function ($id) {
                    if ($id === 'dbal.driver_ids_by_driver_name') {
                        return [
                            'test' => 'dbal.driver.test',
                        ];
                    } elseif ($id === 'dbal.parameters_by_database_connection_name') {
                        return [
                            'default' => [
                                'driver' => 'test',
                            ],
                        ];
                    }
                }
            )
        );

        $containerMock->method(
            'get'
        )->will(
            $this->returnCallback(
                function ($id) use ($driverMock) {
                    if ($id === 'dbal.driver.test') {
                        return $driverMock;
                    }
                }
            )
        );

        return new DatabaseConnectionManager($containerMock);
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
