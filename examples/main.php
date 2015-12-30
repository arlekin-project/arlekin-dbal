#!/usr/bin/env php
<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Examples;

use Arlekin\Dbal\Driver\Pdo\MySql\Driver;
use Arlekin\Dbal\Registry;

$aTime = microtime(true);

require_once __DIR__.'/../tests/bootstrap.php';

$dbal = new Registry($_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']);

$dbal->registerDriver('pdo.mysql', new Driver());

$connection = $dbal->getConnectionWithName('pdo_mysql_test');

$connection->connect();

echo sprintf(
    'Connected.%s',
    PHP_EOL
);

$resultSet = $connection->executeQuery('SELECT 1 AS result FROM Dual');

echo sprintf(
    'Selected %s from Dual.%s',
    $resultSet[0]['result'],
    PHP_EOL
);

$connection->disconnect();

echo sprintf(
    'Disconnected.%s',
    PHP_EOL
);

$bTime = microtime(true);

echo sprintf(
    'Example executed in %s seconds.%s',
    $bTime - $aTime,
    PHP_EOL
);
