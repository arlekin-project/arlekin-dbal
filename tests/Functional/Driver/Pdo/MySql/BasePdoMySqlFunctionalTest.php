<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Functional\Driver\Pdo\MySql;

use Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Calam\Dbal\Tests\BaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
abstract class BasePdoMySqlFunctionalTest extends BaseTest
{    
    /**
     * @var DatabaseConnection
     */
    protected $databaseConnection;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->databaseConnection = new DatabaseConnection(
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test']['host'],
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test']['port'],
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test']['database'],
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test']['user'],
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test']['password']
        );
        
        $this->databaseConnection->connect();
        
        $this->databaseConnection->dropAllDatabaseStructure();
    }
}
