<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Functional\Driver\Pdo\MySql;

use Arlekin\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Arlekin\Dbal\Tests\BaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
abstract class BasePdoMySqlFunctionalTest extends BaseTest
{    
    /**
     * @var DatabaseConnection
     */
    protected $databaseConnection;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->databaseConnection = new DatabaseConnection(
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections'][0]['host'],
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections'][0]['port'],
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections'][0]['database'],
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections'][0]['user'],
            $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections'][0]['password']
        );
        
        $this->databaseConnection->connect();
        
        $this->databaseConnection->dropAllDatabaseStructure();
    }
}
