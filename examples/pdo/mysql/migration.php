#!/usr/bin/env php
<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Examples;

use Arlekin\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Arlekin\Dbal\Driver\Pdo\MySql\Driver;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ColumnType;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager;
use Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager;
use Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder;
use Arlekin\Dbal\Registry;

$aTime = microtime(true);

require_once __DIR__.'/../../../tests/bootstrap.php';

$dbal = new Registry($_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']);

$dbal->registerDriver('pdo.mysql', new Driver());

$connection = $dbal->getConnectionWithName('pdo_mysql_test');

/* @var $connection DatabaseConnection */

$newSchema = new Schema();

$table = new Table();

$table->setName('Foo');

$column = new Column();

$column->setName('foo');
$column->setType(ColumnType::TYPE_INT);
$column->setNullable(true);

$table->addColumn($column);

$newSchema->addTable($table);

$tableManager = new TableManager();

$schemaManager = new SchemaManager();

$migrationQueriesBuilder = new MigrationQueriesBuilder($tableManager, $schemaManager);

echo 'Creating migration queries.'.PHP_EOL;

$migrationQueries = $migrationQueriesBuilder->getMigrationSqlQueries(new Schema(), $newSchema);

echo 'Migration queries:'.PHP_EOL;

foreach ($migrationQueries as $query) {
    echo '    '.$query.PHP_EOL;
}

$connection->connect();

echo sprintf(
    'Connected.%s',
    PHP_EOL
);

echo 'Droping dev database "'.$_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections'][0]['database'].'"...'.PHP_EOL;

$connection->dropAllDatabaseStructure();

$connection->executeMultipleQueries($migrationQueries);

echo 'Execute query: DESC Foo'.PHP_EOL;

$rs = $connection->executeQuery('DESC Foo');

echo 'Result:'.PHP_EOL;

foreach ($rs[0] as $field => $value) {
    echo '    '.$field.': '.$value.PHP_EOL;
}

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
