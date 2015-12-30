<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Migration\Tests\Manager;

use Arlekin\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ColumnType;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager;
use Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder;
use Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager;
use Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager;
use Arlekin\Dbal\Tests\Driver\Pdo\MySql\AbstractBasePdoMySqlTest;

class MigrationManagerTest extends AbstractBasePdoMySqlTest
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
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::__construct
     */
    public function testConstruct()
    {
        $migrationManager = $this->getMigrationManagerForBaseSourceSchema();

        $this->assertAttributeInstanceOf(DatabaseConnection::class, 'databaseConnection', $migrationManager);
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::migrate
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::createMigrationTableIfNotExists
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
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::migrate
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::versionApplied
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::getMigrationTableName
     */
    public function testMigrateSimple()
    {
        $tableManager = new TableManager();

        $migrationQueriesBuilder = new MigrationQueriesBuilder($tableManager);

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
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::migrate
     */
    public function testMigrateSimpleTwice()
    {
        $tableManager = new TableManager();

        $migrationQueriesBuilder = new MigrationQueriesBuilder($tableManager);

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
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\MigrationManager::migrate
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
        $this->databaseConnectionMock = $this->getMock(DatabaseConnection::class, [], [], '', false);

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
        )->setType(
            ColumnType::TYPE_INT
        )->setNullable(
            false
        );

        $column1 = new Column();

        $column1->setName(
            'test2'
        )->setType(
            ColumnType::TYPE_INT
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
