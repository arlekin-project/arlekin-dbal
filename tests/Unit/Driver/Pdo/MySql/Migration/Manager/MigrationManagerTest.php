<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Migration\Manager;

use Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\ColumnDataTypes;
use Calam\Dbal\Driver\Pdo\MySql\Element\Schema;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder;
use Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager;
use Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager;
use Calam\Dbal\Tests\BaseTest;

class MigrationManagerTest extends BaseTest
{
    /**
     * @var int
     */
    protected static $countMigrations;

    /**
     * @var string
     */
    protected $tempMigrationFolder;

    /**
     * @var DatabaseConnection
     */
    protected $databaseConnectionMock;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$countMigrations = 0;
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::__construct
     */
    public function testConstruct()
    {
        $migrationManager = $this->getMigrationManagerForBaseSourceSchema();

        $this->assertAttributeInstanceOf(DatabaseConnection::class, 'databaseConnection', $migrationManager);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::migrate
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::createMigrationTableIfNotExists
     */
    public function testMigrateBase()
    {
        $migrationManager = $this->getMigrationManagerForBaseSourceSchema();

        $result = $migrationManager->migrate($this->tempMigrationFolder);

        $this->assertSame($result['result'], 'success');
        $this->assertSame($result['info']['executedMigrationsCount'], 0);
        $this->assertSame($result['info']['executedMigrationsQueriesCount'], 0);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::migrate
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::versionApplied
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::getMigrationTableName
     */
    public function testMigrateSimple()
    {
        $migrationQueriesBuilder = new MigrationQueriesBuilder();

        $diffManager = $this->getDiffManagerWithVersionGenerator($migrationQueriesBuilder);

        $sourceSchema = $this->getBaseSourceSchema();

        $destinationSchema = new Schema();

        $diffManager->generateDiffFile($sourceSchema, $destinationSchema, $this->tempMigrationFolder);

        $migrationManager = $this->getMigrationManagerForBaseSourceSchema();

        $this->databaseConnectionMock->method(
            'executeQuery'
        )->will(
            $this->returnCallback(
                function ($qry) {
                    if ($qry instanceof Query && $qry->getSql() === 'SELECT COUNT(`version`) as count FROM `_migration` WHERE `version` = :version') {
                        $resultSet = new ResultSet();
                        $row = new ResultRow();

                        $row->setData(
                            [
                                'count' => 0,
                            ]
                        );

                        $resultSet->setRows(
                            [
                                $row,
                            ]
                        );

                        return $resultSet;
                    }
                }
            )
        );

        $result = $migrationManager->migrate($this->tempMigrationFolder);

        $this->assertSame($result['result'], 'success');
        $this->assertSame($result['info']['executedMigrationsCount'], 1);
        $this->assertSame($result['info']['executedMigrationsQueriesCount'], 2);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::migrate
     */
    public function testMigrateSimpleTwice()
    {
        $migrationQueriesBuilder = new MigrationQueriesBuilder();

        $diffManager = $this->getDiffManagerWithVersionGenerator($migrationQueriesBuilder);

        $sourceSchema = $this->getBaseSourceSchema();

        $destinationSchema = new Schema();

        $diffManager->generateDiffFile($destinationSchema, $sourceSchema, $this->tempMigrationFolder);

        $diffManager->generateDiffFile($sourceSchema, $destinationSchema, $this->tempMigrationFolder);

        $migrationManager = $this->getMigrationManagerForBaseSourceSchema();

        $this->databaseConnectionMock->method(
            'executeQuery'
        )->will(
            $this->returnCallback(
                function ($qry) {
                    if ($qry instanceof Query && $qry->getSql() === 'SELECT COUNT(`version`) as count FROM `_migration` WHERE `version` = :version') {
                        $resultSet = new ResultSet();

                        $row = new ResultRow();

                        $row->setData(
                            [
                                'count' => 0,
                            ]
                        );

                        $resultSet->setRows(
                            [
                                $row,
                            ]
                        );

                        return $resultSet;
                    }
                }
            )
        );

        $result = $migrationManager->migrate($this->tempMigrationFolder);

        $this->assertSame($result['result'], 'success');
        $this->assertSame($result['info']['executedMigrationsCount'], 2);
        $this->assertSame($result['info']['executedMigrationsQueriesCount'], 4);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::migrate
     * @expectedException \Exception
     * @expectedExceptionMessage $migrationsFolderFullPath may not be empty.
     */
    public function testMigrateErrorIfMigrationsFolderFullPathIsEmpty()
    {
        $migrationManager = $this->getMigrationManagerForBaseSourceSchema();

        $migrationManager->migrate('');
    }

    /**
     * @return DiffManager
     */
    protected function getDiffManagerWithVersionGenerator(MigrationQueriesBuilder $migrationQueriesBuilder)
    {
        $diffManager = new DiffManager(
            $migrationQueriesBuilder
        );

        $diffManager->setVersionGenerator(
            function () {
                self::$countMigrations += 1;

                return self::$countMigrations;
            }
        );

        return $diffManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $tempMigrationFolder = sprintf(
            '%s/ArlekinMigrations',
            sys_get_temp_dir()
        );

        if (!file_exists($tempMigrationFolder)) {
            mkdir($tempMigrationFolder);
        } else {
            passthru(
                sprintf(
                    'rm -rf %s/*',
                    $tempMigrationFolder
                )
            );
        }

        $this->tempMigrationFolder = $tempMigrationFolder;
    }

    /**
     * @return MigrationManager
     */
    protected function getMigrationManagerForBaseSourceSchema()
    {
        $this->databaseConnectionMock = $this->createMock(DatabaseConnection::class, [], [], '', false);

        return new MigrationManager($this->databaseConnectionMock);
    }

    /**
     * @return Schema
     */
    protected function getBaseSourceSchema()
    {
        $sourceSchema = new Schema();

        $column = new Column();

        $column->setName(
            'test'
        )->setDataType(
            ColumnDataTypes::TYPE_INT
        )->setNullable(
            false
        );

        $column1 = new Column();

        $column1->setName(
            'test2'
        )->setDataType(
            ColumnDataTypes::TYPE_INT
        )->setNullable(
            false
        );

        $table = new Table();

        $table->setName(
            'test'
        )->addColumn(
            $column
        );

        $table1 = new Table();

        $table1->setName(
            'test2'
        )->addColumn(
            $column1
        );

        $sourceSchema->setTables(
            [
                $table,
                $table1,
            ]
        );

        return $sourceSchema;
    }
}
