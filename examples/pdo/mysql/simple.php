#!/usr/bin/env php
<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Examples;

use Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Calam\Dbal\Driver\Pdo\MySql\Driver;
use Calam\Dbal\Driver\Pdo\MySql\Log\JsonFileAppendQueryLogger;
use Calam\Dbal\Registry;

$aTime = microtime(true);

require_once __DIR__.'/../../../tests/bootstrap.php';

$logFile = '/tmp/log'.uniqid();

$_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test']['logger'] = new JsonFileAppendQueryLogger($logFile);

$dbalConf = $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal'];

$dbal = new Registry($dbalConf);

$dbal->registerDriver('pdo.mysql', new Driver());

$connection = $dbal->getConnectionWithName('pdo_mysql_test');

$connection->connect();

echo 'Connected.'.PHP_EOL;

/* @var $connection DatabaseConnection */

$resultSet = $connection->executeQuery(
    'SELECT 1 AS result FROM Dual',
    [],
    [
        'log' => [
            'payload' => [
                'requestId' => 'foobar',
            ],
        ],
    ]
);

echo sprintf(
    'Selected %s from Dual.%s',
    $resultSet[0]['result'],
    PHP_EOL
);

$connection->disconnect();

echo 'Disconnected.'.PHP_EOL;

$bTime = microtime(true);

echo sprintf(
    'Example executed in %s seconds.%s',
    $bTime - $aTime,
    PHP_EOL
);

echo "Logged queries in {$logFile} file.".PHP_EOL;
echo 'File content was:'.PHP_EOL;

passthru("cat {$logFile}");

echo PHP_EOL;

unlink($logFile);