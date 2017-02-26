<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql;

use Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Calam\Dbal\Tests\BaseTest;
use Calam\Dbal\Tests\Helper\CommonTestHelper;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class DatabaseConnectionTest extends BaseTest
{
    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::__construct
     */
    public function testConstruct()
    {
        $this->getPdoMysqlDatabaseConnection();

        $this->assertTrue(true);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::isConnected
     */
    public function testIsConnected()
    {
        $mysqlDatabaseConnection = $this->getPdoMysqlDatabaseConnection();

        $this->assertFalse(
            $mysqlDatabaseConnection->isConnected()
        );

        $mysqlDatabaseConnection->connect();

        $this->assertTrue(
            $mysqlDatabaseConnection->isConnected()
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::connect
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::disconnect
     */
    public function testConnectAndDisconnect()
    {
        $mysqlDatabaseConnection = $this->getPdoMysqlDatabaseConnection();

        $mysqlDatabaseConnection->connect();

        $mysqlDatabaseConnection->disconnect();

        $this->assertTrue(true);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::connect
     */
    public function testConnectPortNotSpecified()
    {
        $mysqlDatabaseConnection = new DatabaseConnection(
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test']['host'],
            null,
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test']['database'],
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test']['user'],
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test']['password']
        );

        $mysqlDatabaseConnection->connect();
        $mysqlDatabaseConnection->disconnect();

        $this->assertTrue(true);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::connect
     */
    public function testConnectExceptionIfAlreadyConnected()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Connection already established.');

        $mysqlDatabaseConnection = $this->getPdoMysqlDatabaseConnection();

        $mysqlDatabaseConnection->connect();
        $mysqlDatabaseConnection->connect();
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::disconnect
     */
    public function testDisconnectExceptionIfAlreadyDisconnected()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Connection already closed.');

        $mysqlDatabaseConnection = $this->getPdoMysqlDatabaseConnection();

        $mysqlDatabaseConnection->disconnect();
        $mysqlDatabaseConnection->disconnect();
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::connectIfNotConnected
     */
    public function testConnectIfNotConnected()
    {
        $mysqlDatabaseConnection = $this->getPdoMysqlDatabaseConnection();

        //first call to connect
        $mysqlDatabaseConnection->connectIfNotConnected();
        //second one to make sure no error is thrown if connection already established
        $mysqlDatabaseConnection->connectIfNotConnected();

        $mysqlDatabaseConnection->disconnect();

        $this->assertTrue(true);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::executeQuery
     */
    public function testExecuteQueryErrorIfNotConnected()
    {
        $mysqlDatabaseConnection = $this->getPdoMysqlDatabaseConnection();

        CommonTestHelper::assertExceptionThrown(
            function () use ($mysqlDatabaseConnection) {
                $mysqlDatabaseConnection->executeQuery('');
            },
            \Exception::class,
            'Trying to execute a query using a non-established connection.'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->connection = $this->getPdoMysqlDatabaseConnection();

        $this->connection->connect();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->connection->disconnect();
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::executeQuery
     */
    public function testExecuteQueryResultSetExpected()
    {
        $rows = $this->connection->executeQuery('SELECT 1 AS success FROM DUAL');

        $this->assertTrue(
            isset($rows[0])
        );

        $row = $rows[0];

        $this->assertEquals('1', $row['success']);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::executeQuery
     */
    public function testExecuteBoundParameterOneParameter()
    {
        $rows = $this->connection->executeQuery(
            'SELECT 1 AS success FROM DUAL WHERE 1 = :test',
            [
                'test' => '1',
            ]
        );

        $this->assertTrue(isset($rows[0]));

        $row = $rows[0];

        $this->assertSame('1', $row['success']);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::executeQuery
     */
    public function testExecuteBoundParameterTwoParametersSetParameterTwice()
    {
        $rows = $this->connection->executeQuery(
            'SELECT 1 AS success FROM DUAL WHERE 1 = :test AND 42 = :test2',
            [
                'test' => '1',
                'test2' => '42',
            ]
        );

        $this->assertTrue(
            isset($rows[0])
        );

        $row = $rows[0];

        $this->assertEquals('1', $row['success']);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::executeQuery
     */
    public function testExecuteBoundParameterTwoParametersSetParametersArray()
    {
        $rows = $this->connection->executeQuery(
            'SELECT 1 AS success FROM DUAL WHERE 1 = :test AND 42 = :test2',
            [
                'test' => '1',
                'test2' => '42',
            ]
        );

        $this->assertTrue(isset($rows[0]));

        $row = $rows[0];

        $this->assertEquals('1', $row['success']);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::executeQuery
     */
    public function testExecuteBoundParameterArrayParameter()
    {
        $rows = $this->connection->executeQuery(
            'SELECT 1 AS success FROM DUAL WHERE 2 IN (:test)',
            [
                'test' => [
                    1,
                    2,
                ],
            ]
        );

        $this->assertTrue(isset($rows[0]));

        $row = $rows[0];

        $this->assertEquals('1', $row['success']);
    }

    protected function doTestExecuteMultipleQueries(array $queries)
    {
        $rows = $this->connection->executeMultipleQueries($queries);

        $rows0 = $rows[0];
        $row00 = $rows0[0];
        $rows1 = $rows[1];
        $row10 = $rows1[0];

        $this->assertEquals(
            '1',
            $row00['success']
        );

        $this->assertEquals(
            '2',
            $row10['success']
        );

        $this->assertInternalType('array', $rows);
    }
    
    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::executeMultipleQueries
     */
    public function testExecuteMultipleQueries()
    {
        $this->doTestExecuteMultipleQueries(
            [
                'SELECT 1 AS success FROM DUAL',
                'SELECT 2 AS success FROM DUAL',
            ]
        );
    }
    
    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::executeMultipleQueries
     */
    public function testExecuteMultipleQueriesWithParameters()
    {
        $this->doTestExecuteMultipleQueries(
            [
                [
                    'SELECT 1 AS success FROM DUAL WHERE 1 = :param',
                    [
                        'param' => '1',
                    ]
                ],
                'SELECT 2 AS success FROM DUAL',
            ]
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::executeMultipleQueries
     */
    public function testExecuteMultipleQueriesWithError()
    {
        $cases = [
            [
                'query' => 'zsqd',
            ],
        ];

        foreach ($cases as $case) {
            $query = $case['query'];

            $expectionThrown = false;

            try {
                $this->connection->executeMultipleQueries(
                    [
                        $query,
                    ]
                );
            } catch (\Exception $ex) {
                $expectionThrown = true;

                $this->assertSame(
                    "Error executing query: $query",
                    $ex->getMessage()
                );
            }

            $this->assertTrue($expectionThrown);
        }
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::dropAllTables
     */
    public function testDropAllTables()
    {
        $this->connection->dropAllTables();

        $this->assertTrue(true);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::dropAllViews
     */
    public function testDropAllViews()
    {
        $this->connection->dropAllTables();

        $this->assertTrue(true);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection::dropAllDatabaseStructure
     */
    public function testDropAllDatabaseStructure()
    {
        $this->connection->dropAllDatabaseStructure();

        $this->assertTrue(true);
    }

    /**
     * @return DatabaseConnection
     */
    protected function getPdoMysqlDatabaseConnection($envDbal = [])
    {
        $testParametersEnvDbal = $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test'];

        $parameters = array_merge($testParametersEnvDbal, $envDbal);

        return new DatabaseConnection(
            $parameters['host'],
            $parameters['port'],
            $parameters['database'],
            $parameters['user'],
            $parameters['password']
        );
    }
}
