<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections'] = [
    [
        'name' => 'pdo_mysql_test',
        'driver' => 'pdo.mysql',
        'host' => 'mysql',
        'port' => 3306,
        'database' => 'arlekin',
        'user' => 'arlekin',
        'password' => 'arlekin',
    ],
];
