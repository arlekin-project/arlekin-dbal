<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test'] = [
    'name' => 'pdo_mysql_test',
    'driver' => 'pdo.mysql',
    'host' => 'calam_mariadb_1',
    'port' => 3306,
    'database' => 'calam',
    'user' => 'calam',
    'password' => 'calam',
];

$_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['root'] = [
    'name' => 'root',
    'driver' => 'pdo.mysql',
    'host' => 'calam_mariadb_1',
    'port' => 3306,
    'user' => 'root',
    'password' => 'root',
];
