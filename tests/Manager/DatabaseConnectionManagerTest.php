<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\Tests\Manager;

use Arlecchino\DatabaseAbstractionLayer\DatabaseConnectionInterface;
use Arlecchino\DatabaseAbstractionLayer\DriverInterface;
use Arlecchino\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager;
use Arlecchino\Core\Tests\Helper\CommonTestHelper;
use Exception;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DatabaseConnectionManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DatabaseConnectionManager
     */
    protected $databaseConnectionManager;

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::__construct
     */
    public function test__construct()
    {
        $containerMock = $this->getMock(
            ContainerInterface::class
        );

        $instance = new DatabaseConnectionManager(
            $containerMock
        );

        $this->assertAttributeInstanceOf(
            ContainerInterface::class,
            'container',
            $instance
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::instanciateDatabaseConnection
     */
    public function testInstanciateDatabaseConnection()
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
                function (
                    $id
                ) {
                    if ($id === 'dbal.driver_ids_by_driver_name') {
                        return array(
                            'test' => 'dbal.driver.test'
                        );
                    }
                }
            )
        );

        $containerMock->method(
            'get'
        )->will(
            $this->returnCallback(
                function (
                    $id
                ) use (
                    $driverMock
                ) {
                    if ($id === 'dbal.driver.test') {
                        return $driverMock;
                    }
                }
            )
        );

        $databaseConnectionManager = new DatabaseConnectionManager(
            $containerMock
        );

        $instance = $databaseConnectionManager->instanciateDatabaseConnection(
            array(
                'driver' => 'test'
            )
        );

        $this->assertInstanceOf(
            get_class(
                $databaseConnectionMock
            ),
            $instance
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::instanciateDatabaseConnection
     */
    public function testInstanciateDatabaseConnectionExceptionIfNoDriverFound()
    {
        $containerMock = $this->getMock(
            ContainerInterface::class
        );

        $containerMock->method(
            'getParameter'
        )->will(
            $this->returnCallback(
                function (
                    $id
                ) {
                    if ($id === 'dbal.driver_ids_by_driver_name') {
                        return array();
                    }
                }
            )
        );

        $databaseConnectionManager = new DatabaseConnectionManager(
            $containerMock
        );

        CommonTestHelper::assertExceptionThrown(
            function () use (
                $databaseConnectionManager
            ) {
                $databaseConnectionManager->instanciateDatabaseConnection(
                    array(
                        'driver' => 'test'
                    )
                );
            },
            Exception::class,
            'Found no driver with name "test".'
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::instanciateNamedDatabaseConnection
     */
    public function testInstanciateNamedDatabaseConnection()
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
                function (
                    $id
                ) {
                    if ($id === 'dbal.driver_ids_by_driver_name') {
                        return array(
                            'test' => 'dbal.driver.test'
                        );
                    } elseif ($id === 'dbal.parameters_by_database_connection_name') {
                        return array(
                            'default' => array(
                                'driver' => 'test'
                            )
                        );
                    }
                }
            )
        );

        $containerMock->method(
            'get'
        )->will(
            $this->returnCallback(
                function (
                    $id
                ) use (
                    $driverMock
                ) {
                    if ($id === 'dbal.driver.test') {
                        return $driverMock;
                    }
                }
            )
        );

        $databaseConnectionManager = new DatabaseConnectionManager(
            $containerMock
        );

        $instance = $databaseConnectionManager->instanciateNamedDatabaseConnection(
            'default'
        );

        $this->assertInstanceOf(
            get_class(
                $databaseConnectionMock
            ),
            $instance
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager::instanciateNamedDatabaseConnection
     */
    public function testInstanciateNamedDatabaseConnectionExceptionIfNoDriverFound()
    {
        $containerMock = $this->getMock(
            ContainerInterface::class
        );

        $containerMock->method(
            'getParameter'
        )->will(
            $this->returnCallback(
                function (
                    $id
                ) {
                    if ($id === 'dbal.driver_ids_by_driver_name') {
                        return array(
                            'test' => 'dbal.driver.test'
                        );
                    } elseif ($id === 'dbal.parameters_by_database_connection_name') {
                        return array();
                    }
                }
            )
        );

        $databaseConnectionManager = new DatabaseConnectionManager(
            $containerMock
        );

        CommonTestHelper::assertExceptionThrown(
            function () use (
                $databaseConnectionManager
            ) {
                $databaseConnectionManager->instanciateNamedDatabaseConnection(
                    'default'
                );
            },
            Exception::class,
            'Found no database connection with name "default".'
        );
    }
}
