<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Tests;

use Arlekin\Dbal\Tests\AbstractBaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
abstract class AbstractBasePdoMySqlTest extends AbstractBaseTest
{
    protected function setUp()
    {
        parent::setUp();

        $rootParameters = $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['root'];
        $parameters = $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections'][0];

        $dsn = sprintf(
            'mysql:host=%s;port=%s',
            $rootParameters['host'],
            $rootParameters['port']
        );

        $pdo = new \PDO(
            $dsn,
            $rootParameters['user'],
            $rootParameters['password']
        );

        $pdo->exec(
            sprintf(
                "DROP DATABASE `%s`;",
                $parameters['database']
            )
        );

        $pdo->exec(
            sprintf(
                "CREATE DATABASE `%s`;",
                $parameters['database']
            )
        );
    }
}
