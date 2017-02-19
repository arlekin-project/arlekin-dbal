<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Migration\Builder;

use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\ColumnType;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnDeleteConstraint;
use Calam\Dbal\Driver\Pdo\MySql\Element\Index;
use Calam\Dbal\Driver\Pdo\MySql\Element\IndexKind;
use Calam\Dbal\Driver\Pdo\MySql\Element\PrimaryKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\Schema;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Driver\Pdo\MySql\Element\View;
use Calam\Dbal\Driver\Pdo\MySql\Manager\TableManager;
use Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder;
use Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\SchemaBuilder;
use Calam\Dbal\Tests\BaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class MigrationQueriesBuilderTest extends BaseTest
{
    /**
     * @var TableManager
     */
    protected $tableManager;

    /**
     * @var MigrationQueriesBuilder
     */
    protected $sqlMigrationBuilder;

    /**
     * @var SchemaBuilder
     */
    protected $schemaBuilder;

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropColumnsQueries
     */
    public function testBuildSchemaColumnsNoChange()
    {
        $commonTable = new Table();

        $commonTable->setName('testTable');

        $fooColumn = new Column();

        $fooColumn->setName(
            'foo'
        )->setType(
            ColumnType::TYPE_INT
        )->setNullable(
            false
        )->setAutoIncrement(
            false
        );

        $commonTable->addColumn($fooColumn);

        $sourceSchema = new Schema();

        $sourceSchema->addTable($commonTable);

        $destinationSchema = new Schema();

        $destinationSchema->addTable($commonTable);

        $this->assertEquals(
            [],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropTableQueries
     */
    public function testBuildFromSchemaDropNonExistingInSchemaTable()
    {
        $sourceTable = new Table();

        $sourceTable->setName('testTable');

        $sourceSchema = new Schema();

        $sourceSchema->addTable($sourceTable);

        $destinationSchema = new Schema();

        $this->assertEquals(
            [
                'DROP TABLE `testTable`',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropColumnsQueries
     */
    public function testBuildFromSchemaDropNonExistingInSchemaColumn()
    {
        $sourceTable = new Table();

        $sourceTable->setName('testTable');

        $sourceColumn = new Column();

        $sourceColumn->setName('testColumn');

        $sourceTable->addColumn($sourceColumn);

        $destinationTable = new Table();

        $destinationTable->setName('testTable');

        $sourceSchema = new Schema();

        $sourceSchema->addTable($sourceTable);

        $destinationSchema = new Schema();

        $destinationSchema->addTable($destinationTable);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` DROP COLUMN `testColumn`',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropColumnsQueries
     */
    public function testBuildFromSchemaDropAndCreateDifferentColumn()
    {
        $sourceTable = new Table();

        $sourceTable->setName('testTable');

        $sourceColumn = new Column();

        $sourceColumn->setName('testColumn');

        $sourceTable->addColumn($sourceColumn);

        $destinationTable = new Table();

        $destinationTable->setName('testTable');

        $destinationColumn = new Column();

        $destinationColumn->setName(
            'testColumn'
        )->setType(
            ColumnType::TYPE_DATE
        )->setNullable(
            false
        );

        $destinationTable->addColumn($destinationColumn);

        $sourceSchema = new Schema();

        $sourceSchema->addTable($sourceTable);

        $destinationSchema = new Schema();

        $destinationSchema->addTable($destinationTable);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` DROP COLUMN `testColumn`',
                'ALTER TABLE `testTable` ADD COLUMN `testColumn` DATE NOT NULL',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropIndexesQueries
     */
    public function testBuildSchemaIndexesNoChange()
    {
        $commonTable = new Table();

        $commonTable->setName('testTable');

        $fooColumn = new Column();

        $fooColumn->setName(
            'foo'
        )->setType(
            ColumnType::TYPE_INT
        )->setNullable(
            false
        )->setAutoIncrement(
            false
        );

        $commonTable->addColumn($fooColumn);

        $index = new Index();

        $index->setColumns(
            [
                $fooColumn,
            ]
        )->setKind(
            IndexKind::KIND_BTREE
        );

        $commonTable->addIndex($index);

        $sourceSchema = new Schema();

        $sourceSchema->addTable($commonTable);

        $destinationSchema = new Schema();

        $destinationSchema->addTable($commonTable);

        $this->assertEquals(
            [],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropIndexesQueries
     */
    public function testBuildFromSchemaDropNonExistingInSchemaIndex()
    {
        $sourceTable = new Table();

        $destinationTable = new Table();

        $sourceTable->setName('testTable');

        $destinationTable->setName('testTable');

        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceSchema->addTable($sourceTable);

        $destinationSchema->addTable($destinationTable);

        $sourceIndex = new Index();

        $sourceIndex->setName('testIndexName');

        $sourceTable->addIndex($sourceIndex);

        $this->assertEquals(
            [
                'DROP INDEX testIndexName ON `testTable`',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropIndexesQueries
     */
    public function testBuildFromSchemaDropAndCreateDifferentIndex()
    {
        $column = new Column();

        $column->setName('testColumnName');

        $sourceTable = new Table();

        $destinationTable = new Table();

        $sourceTable->addColumn($column);

        $destinationTable->addColumn($column);

        $sourceTable->setName('testTable');

        $destinationTable->setName('testTable');

        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceSchema->addTable($sourceTable);

        $destinationSchema->addTable($destinationTable);

        $sourceIndex = new Index();

        $sourceIndex->setName('testIndexName');

        $destinationIndex = new Index();

        $destinationIndex->setName(
            'testIndexName'
        )->setKind(
            IndexKind::KIND_BTREE
        )->addColumn(
            $column
        );

        $sourceTable->addIndex($sourceIndex);

        $destinationTable->addIndex($destinationIndex);

        $this->assertEquals(
            [
                'DROP INDEX testIndexName ON `testTable`',
                'ALTER TABLE `testTable` ADD INDEX `testIndexName` (`testColumnName`) USING BTREE',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropForeignKeysQueries
     */
    public function testBuildFromSchemaDropNonExistingInSchemaForeignKey()
    {
        $sourceTable = new Table();

        $destinationTable = new Table();

        $referencedSourceTable = new Table();

        $referencedDestinationTable = new Table();

        $sourceTable->setName('testTable');

        $destinationTable->setName('testTable');

        $referencedSourceTable->setName('testReferencedTable');

        $referencedDestinationTable->setName('testReferencedTable');

        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceSchema->addTable(
            $sourceTable
        )->addTable(
            $referencedSourceTable
        );

        $destinationSchema->addTable(
            $destinationTable
        )->addTable(
            $referencedDestinationTable
        );

        $foreignKey = new ForeignKey();

        $sourceTable->addForeignKey($foreignKey);

        $foreignKey->setReferencedTable($referencedSourceTable);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` DROP FOREIGN KEY `fk_d9f310b2d2086ccc77567a214abbcbe96cd077bb`',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableAddPrimaryKeyQuery
     */
    public function testBuildFromSchemaCreateNonExistingInDatabasePrimaryKey()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceTable = new Table();

        $destinationTable = new Table();

        $testColumn = new Column();

        $testColumn1 = new Column();

        $sourceTable->setName('testTable');

        $destinationTable->setName('testTable');

        $sourceTable->setColumns(
            [
                $testColumn,
                $testColumn1,
            ]
        );

        $destinationTable->setColumns(
            [
                $testColumn,
                $testColumn1,
            ]
        );

        $testColumn->setName('testColumn');

        $testColumn1->setName('testColumn1');

        $sourceSchema->addTable($sourceTable);

        $destinationSchema->addTable($destinationTable);

        $destinationPrimaryKey = new PrimaryKey();

        $destinationPrimaryKey->setColumns(
            [
                $testColumn,
                $testColumn1,
            ]
        );

        $destinationTable->setPrimaryKey($destinationPrimaryKey);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` ADD PRIMARY KEY (`testColumn`, `testColumn1`)',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropPrimaryKeyQueries
     */
    public function testBuildFromSchemaDropNonExistingInSchemaPrimaryKey()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceTable = new Table();

        $destinationTable = new Table();

        $testColumn = new Column();

        $testColumn1 = new Column();

        $sourceTable->setName('testTable');

        $destinationTable->setName('testTable');

        $sourceTable->setColumns(
            [
                $testColumn,
                $testColumn1,
            ]
        );

        $destinationTable->setColumns(
            [
                $testColumn,
                $testColumn1,
            ]
        );

        $testColumn->setName('testColumn');

        $testColumn1->setName('testColumn1');

        $sourceSchema->addTable($sourceTable);

        $destinationSchema->addTable($destinationTable);

        $sourcePrimaryKey = new PrimaryKey();

        $sourcePrimaryKey->setColumns(
            [
                $testColumn,
                $testColumn1,
            ]
        );

        $sourceTable->setPrimaryKey($sourcePrimaryKey);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` DROP PRIMARY KEY',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropPrimaryKeyQueries
     */
    public function testBuildFromSchemaDropAndCreateDifferentPrimaryKey()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceTable = new Table();

        $destinationTable = new Table();

        $testColumn = new Column();

        $testColumn1 = new Column();

        $sourceTable->setName('testTable');

        $destinationTable->setName('testTable');

        $sourceTable->setColumns(
            [
                $testColumn,
                $testColumn1,
            ]
        );

        $destinationTable->setColumns(
            [
                $testColumn,
                $testColumn1,
            ]
        );

        $testColumn->setName('testColumn');

        $testColumn1->setName('testColumn1');

        $sourceSchema->addTable($sourceTable);

        $destinationSchema->addTable($destinationTable);

        $sourcePrimaryKey = new PrimaryKey();

        $sourcePrimaryKey->setColumns(
            [
                $testColumn,
                $testColumn1,
            ]
        );

        $destinationPrimaryKey = new PrimaryKey();

        $destinationPrimaryKey->setColumns(
            [
                $testColumn,
            ]
        );

        $sourceTable->setPrimaryKey($sourcePrimaryKey);

        $destinationTable->setPrimaryKey($destinationPrimaryKey);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` DROP PRIMARY KEY',
                'ALTER TABLE `testTable` ADD PRIMARY KEY (`testColumn`)',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeCreateTableBaseQueries
     */
    public function testBuildFromSchemaCreateNonExistingInDatabaseTable()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $destinationTable = new Table();

        $destinationSchema->addTable($destinationTable);

        $destinationColumn = new Column();

        $destinationColumn->setName(
            'testColumn'
        )->setType(
            ColumnType::TYPE_INT
        )->setNullable(
            false
        );

        $destinationTable->addColumn($destinationColumn);

        $destinationTable->setName('testTable');

        $this->assertEquals(
            [
                'CREATE TABLE `testTable` (`testColumn` INT NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeCreatePrimaryKeysSqlQueries
     */
    public function testBuildFromSchemaCreateNonExistingInDatabasePrimaryKeyTableDoesNotExists()
    {
        $testTable = new Table();

        $testTable->setName('testTable');

        $testColumn = new Column();

        $testColumn->setName(
            'testColumn'
        )->setType(
            ColumnType::TYPE_VARCHAR
        )->setNullable(
            true
        );

        $destinationSchema = new Schema();

        $destinationSchema->addTable($testTable);

        $testTable->addColumn($testColumn);

        $primaryKey = new PrimaryKey();

        $primaryKey->setColumns(
            [
                $testColumn,
            ]
        );

        $testTable->setPrimaryKey($primaryKey);

        $sourceSchema = new Schema();

        $this->assertEquals(
            [
                'CREATE TABLE `testTable` (`testColumn` VARCHAR DEFAULT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin',
                'ALTER TABLE `testTable` ADD PRIMARY KEY (`testColumn`)',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeCreatePrimaryKeysSqlQueries
     */
    public function testBuildFromSchemaCreateNonExistingInDatabasePrimaryKeyTableDoesNotExistsNoPrimaryKey()
    {
        $testTable = new Table();

        $testTable->setName('testTable');

        $testColumn = new Column();

        $testColumn->setName(
            'testColumn'
        )->setType(
            ColumnType::TYPE_VARCHAR
        )->setNullable(
            true
        );

        $destinationSchema = new Schema();

        $destinationSchema->addTable($testTable);

        $testTable->addColumn($testColumn);

        $sourceSchema = new Schema();

        $this->assertEquals(
            [
                'CREATE TABLE `testTable` (`testColumn` VARCHAR DEFAULT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeCreatePrimaryKeysSqlQueries
     */
    public function testBuildFromSchemaCreateNonExistingInDatabasePrimaryKeyTableExists()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceTable = new Table();

        $sourceTable->setName('testTable');

        $destinationTable = new Table();

        $destinationTable->setName('testTable');

        $sourceSchema->addTable($sourceTable);

        $destinationSchema->addTable($destinationTable);

        $destinationColumn = new Column();

        $destinationColumn->setName('testColumn');

        $destinationColumn->setType(ColumnType::TYPE_VARCHAR);

        $destinationColumn->setParameters(
            [
                'length' => 255,
            ]
        );

        $destinationColumn->setNullable(true);

        $sourceTable->addColumn($destinationColumn);

        $destinationTable->addColumn($destinationColumn);

        $primaryKey = new PrimaryKey();

        $primaryKey->setColumns(
            [
                $destinationColumn,
            ]
        );

        $destinationTable->setPrimaryKey($primaryKey);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` ADD PRIMARY KEY (`testColumn`)',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeCreatePrimaryKeysSqlQueries
     */
    public function testBuildFromSchemaCreateNonExistingInDatabasePrimaryKeyTableExistsDoNotCreatePrimaryKey()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceTable = new Table();

        $sourceTable->setName('testTable');

        $destinationTable = new Table();

        $destinationTable->setName('testTable');

        $sourceSchema->addTable($sourceTable);

        $destinationSchema->addTable($destinationTable);

        $destinationColumn = new Column();

        $destinationColumn->setName('testColumn');

        $destinationColumn->setType(ColumnType::TYPE_VARCHAR);

        $destinationColumn->setParameters(
            [
                'length' => 255,
            ]
        );

        $destinationColumn->setNullable(true);

        $sourceTable->addColumn($destinationColumn);

        $destinationTable->addColumn($destinationColumn);

        $primaryKey = new PrimaryKey();

        $primaryKey->setColumns(
            [
                $destinationColumn,
            ]
        );

        $sourceTable->setPrimaryKey($primaryKey);

        $destinationTable->setPrimaryKey($primaryKey);

        $this->assertEmpty(
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeCreateIndexesSqlQueries
     */
    public function testBuildFromSchemaCreateNonExistingInDatabaseIndexTableExists()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceTable = new Table();

        $destinationTable = new Table();

        $sourceTable->setName('testTable');

        $destinationTable->setName('testTable');

        $sourceSchema->addTable($sourceTable);

        $destinationSchema->addTable($destinationTable);

        $column = new Column();

        $column->setName('testColumn');

        $sourceTable->addColumn($column);

        $destinationTable->addColumn($column);

        $destinationIndex = new Index();

        $destinationIndex->addColumn(
            $column
        )->setKind(
            IndexKind::KIND_UNIQUE
        )->setName(
            'unique_testColumn'
        );

        $destinationTable->addIndex($destinationIndex);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` ADD UNIQUE INDEX `unique_testColumn` (`testColumn`)',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeCreateIndexesSqlQueries
     */
    public function testBuildFromSchemaCreateNonExistingInDatabaseIndexTableDoesNotExists()
    {
        $testTable = new Table();

        $testTable->setName('testTable');

        $testColumn = new Column();

        $testColumn->setName(
            'testColumn'
        )->setType(
            ColumnType::TYPE_VARCHAR
        )->setNullable(
            true
        );

        $destinationSchema = new Schema();

        $destinationSchema->addTable($testTable);

        $testTable->addColumn($testColumn);

        $index = new Index();

        $index->setName(
            'unique_testColumn'
        )->setKind(
            IndexKind::KIND_UNIQUE
        )->setColumns(
            [
                $testColumn,
            ]
        );

        $testTable->addIndex($index);

        $sourceSchema = new Schema();

        $this->assertEquals(
            [
                'CREATE TABLE `testTable` (`testColumn` VARCHAR DEFAULT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin',
                'ALTER TABLE `testTable` ADD UNIQUE INDEX `unique_testColumn` (`testColumn`)',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableCreateColumnsQueries
     */
    public function testBuildFromSchemaCreateNonExistingInDatabaseColumn()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceTable = new Table();

        $destinationTable = new Table();

        $sourceTable->setName('testTable');

        $destinationTable->setName('testTable');

        $sourceSchema->addTable($sourceTable);
        $destinationSchema->addTable($destinationTable);

        $destinationColumn = new Column();

        $destinationColumn->setName(
            'testColumn'
        )->setType(
            ColumnType::TYPE_VARCHAR
        )->setNullable(
            false
        )->setParameters(
            [
                'length' => 255,
            ]
        );

        $destinationTable->addColumn($destinationColumn);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` ADD COLUMN `testColumn` VARCHAR(255) NOT NULL',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableCreateForeignKeysQueriesMakeDoForEachForeignKeys
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableCreateForeignKeysQueriesMakeCreateForeignKeySql
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableCreateForeignKeysQueries
     */
    public function testBuildFromSchemaCreateNonExistingInDatabaseForeignKeyTableDoesExists()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceTable = new Table();

        $destinationTable = new Table();

        $referencedTestTable = new Table();

        $sourceTable->setName('testTable');

        $destinationTable->setName('testTable');

        $referencedTestTable->setName('referencedTestTable');

        $sourceSchema->setTables(
            [
                $sourceTable,
                $referencedTestTable,
            ]
        );

        $destinationSchema->setTables(
            [
                $destinationTable,
                $referencedTestTable,
            ]
        );

        $testColumn = new Column();

        $referencedTestColumn = new Column();

        $testColumn->setName('testColumn');

        $referencedTestColumn->setName('referencedTestColumn');

        $destinationForeignKey = new ForeignKey();

        $destinationForeignKey->setReferencedTable(
            $referencedTestTable
        )->setColumns(
            [
                $testColumn,
            ]
        )->setReferencedColumns(
            [
                $referencedTestColumn,
            ]
        )->setOnDelete(
            ForeignKeyOnDeleteConstraint::ON_DELETE_CASCADE
        );

        $sourceTable->setColumns(
            [
                $testColumn,
            ]
        );

        $destinationTable->setColumns(
            [
                $testColumn,
            ]
        );

        $referencedTestTable->setColumns(
            [
                $referencedTestColumn,
            ]
        );

        $destinationTable->addForeignKey($destinationForeignKey);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` ADD CONSTRAINT `fk_30aaa48b80c684b53e00475b4beaa9be2b26b185` FOREIGN KEY (`testColumn`) REFERENCES `referencedTestTable` (`referencedTestColumn`) ON DELETE CASCADE ON UPDATE RESTRICT',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableCreateForeignKeysQueriesMakeDoForEachForeignKeys
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableCreateForeignKeysQueriesMakeCreateForeignKeySql
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableCreateForeignKeysQueries
     */
    public function testBuildFromSchemaCreateNonExistingInDatabaseForeignKeyTableDoesNotExists()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $destinationTable = new Table();

        $referencedTestTable = new Table();

        $destinationTable->setName('testTable');

        $referencedTestTable->setName('referencedTestTable');

        $sourceSchema->setTables(
            [
                $referencedTestTable,
            ]
        );

        $destinationSchema->setTables(
            [
                $destinationTable,
                $referencedTestTable,
            ]
        );

        $testColumn = new Column();

        $referencedTestColumn = new Column();

        $testColumn->setType(ColumnType::TYPE_INT);

        $referencedTestColumn->setType(ColumnType::TYPE_INT);

        $testColumn->setNullable(false);

        $referencedTestColumn->setNullable(false);

        $testColumn->setName('testColumn');

        $referencedTestColumn->setName('referencedTestColumn');

        $destinationForeignKey = new ForeignKey();

        $destinationForeignKey->setReferencedTable(
            $referencedTestTable
        )->setColumns(
            [
                $testColumn,
            ]
        )->setReferencedColumns(
            [
                $referencedTestColumn,
            ]
        )->setOnDelete(
            ForeignKeyOnDeleteConstraint::ON_DELETE_CASCADE
        );

        $destinationTable->setColumns(
            [
                $testColumn,
            ]
        );

        $referencedTestTable->setColumns(
            [
                $referencedTestColumn,
            ]
        );

        $destinationTable->addForeignKey($destinationForeignKey);

        $this->assertEquals(
            [
                'CREATE TABLE `testTable` (`testColumn` INT NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin',
                'ALTER TABLE `testTable` ADD CONSTRAINT `fk_30aaa48b80c684b53e00475b4beaa9be2b26b185` FOREIGN KEY (`testColumn`) REFERENCES `referencedTestTable` (`referencedTestColumn`) ON DELETE CASCADE ON UPDATE RESTRICT',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableSetAutoIncrementQueries
     */
    public function testGetMigrationSqlQueriesAutoIncrementNewColumn()
    {
        $table = new Table();

        $table->setName('test');

        $column = new Column();

        $column->setName('foo');

        $column->setType(ColumnType::TYPE_INT);

        $column->setNullable(false);

        $column->setAutoIncrement(true);

        $table->addColumn($column);

        $primaryKey = new PrimaryKey();

        $primaryKey->setColumns(
            [
                $column,
            ]
        );

        $table->setPrimaryKey($primaryKey);

        $destinationSchema= new Schema();

        $destinationSchema->addTable($table);

        $sourceSchema = new Schema();

        $this->assertEquals(
            [
                'CREATE TABLE `test` (`foo` INT NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin',
                'ALTER TABLE `test` ADD PRIMARY KEY (`foo`)',
                'ALTER TABLE `test` CHANGE `foo` `foo` INT NOT NULL AUTO_INCREMENT',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableSetAutoIncrementQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterTableUnsetAutoIncrementQueries
     */
    public function testGetMigrationSqlQueriesAutoIncrementChange()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceTable = new Table();

        $sourceTable->setName('test');

        $sourceFooColumn = new Column();

        $sourceFooColumn->setName(
            'foo'
        )->setType(
            ColumnType::TYPE_INT
        )->setNullable(
            false
        )->setAutoIncrement(true);

        $sourceBarColumn = new Column();

        $sourceBarColumn->setName(
            'bar'
        )->setType(
            ColumnType::TYPE_INT
        )->setNullable(
            false
        );

        $sourceTable->setColumns(
            [
                $sourceFooColumn,
                $sourceBarColumn,
            ]
        );

        $sourcePrimaryKey = new PrimaryKey();

        $sourcePrimaryKey->setColumns(
            [
                $sourceFooColumn,
            ]
        );

        $sourceTable->setPrimaryKey($sourcePrimaryKey);

        $sourceSchema->addTable($sourceTable);

        $destinationTable = new Table();

        $destinationTable->setName('test');

        $destinationFooColumn = new Column();

        $destinationFooColumn->setName(
            'foo'
        )->setType(
            ColumnType::TYPE_INT
        )->setNullable(
            false
        )->setAutoIncrement(
            false
        );

        $destinationBarColumn = new Column();

        $destinationBarColumn->setName(
            'bar'
        )->setType(
            ColumnType::TYPE_INT
        )->setNullable(
            false
        )->setAutoIncrement(
            true
        );

        $destinationTable->setColumns(
            [
                $destinationFooColumn,
                $destinationBarColumn,
            ]
        );

        $destinationPrimaryKey = new PrimaryKey();

        $destinationPrimaryKey->setColumns(
            [
                $destinationBarColumn,
            ]
        );

        $destinationTable->setPrimaryKey($destinationPrimaryKey);

        $destinationSchema->addTable($destinationTable);

        $this->assertEquals(
            [
                'ALTER TABLE `test` CHANGE `foo` `foo` INT NOT NULL',
                'ALTER TABLE `test` DROP PRIMARY KEY',
                'ALTER TABLE `test` ADD PRIMARY KEY (`bar`)',
                'ALTER TABLE `test` CHANGE `bar` `bar` INT NOT NULL AUTO_INCREMENT',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeCreateViewsQueries
     */
    public function testGetMigrationSqlQueriesNewView()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $view = new View();

        $view->setName(
            'testView'
        )->setDefinition(
            'SELECT `calam`.`test`.`foo` AS `foo` FROM `calam`.`test`'
        );

        $destinationSchema->addView($view);

        $this->assertEquals(
            [
                'CREATE VIEW testView AS SELECT `calam`.`test`.`foo` AS `foo` FROM `calam`.`test`',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeDropViewsQueries
     */
    public function testGetMigrationSqlQueriesDropViews()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceView = new View();

        $sourceView->setName(
            'testView'
        )->setDefinition(
            'CREATE VIEW testView AS SELECT foo FROM test'
        );

        $sourceSchema->addView($sourceView);

        $sqlMigrationQueries = $this->sqlMigrationBuilder->getMigrationSqlQueries($sourceSchema, $destinationSchema);

        $this->assertCount(1, $sqlMigrationQueries);

        $this->assertEquals(
            'drop view testview',
            mb_strtolower(
                $sqlMigrationQueries[0]
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::getMigrationSqlQueries
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder::makeAlterViewsQueries
     */
    public function testGetMigrationSqlQueriesViewDefinitionChange()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceView = new View();

        $sourceView->setName(
            'testView'
        )->setDefinition(
            'SELECT foo FROM test'
        );

        $destinationView = new View();

        $destinationView->setName(
            'testView'
        )->setDefinition(
            'SELECT bar FROM test'
        );

        $destinationSchema->addView($destinationView);

        $sourceSchema->addView($sourceView);

        $sqlMigrationQueries = $this->sqlMigrationBuilder->getMigrationSqlQueries($sourceSchema, $destinationSchema);

        $this->assertCount(1, $sqlMigrationQueries);

        $this->assertEquals(
            'alter view testview as select bar from test',
            mb_strtolower(
                $sqlMigrationQueries[0]
            )
        );
    }

    /**
     * @coversNothing
     */
    public function testGetMigrationSqlQueriesAutoIncrementAndColumnChange()
    {
        $sourceSchema = new Schema();

        $destinationSchema = new Schema();

        $sourceTable = new Table();

        $sourceTable->setName(
            'testTable'
        );

        $sourceFooColumn = new Column();

        $sourceFooColumn->setName(
            'foo'
        )->setType(
            ColumnType::TYPE_INT
        )->setNullable(
            false
        )->setAutoIncrement(
            true
        );

        $sourceTable->addColumn($sourceFooColumn);

        $sourceSchema->addTable($sourceTable);

        $destinationTable = new Table();

        $destinationTable->setName(
            'testTable'
        );

        $destinationFooColumn = new Column();

        $destinationFooColumn->setName(
            'foo'
        )->setType(
            ColumnType::TYPE_INT
        )->setNullable(
            true
        )->setAutoIncrement(
            false
        );

        $destinationTable->addColumn($destinationFooColumn);

        $destinationSchema->addTable($destinationTable);

        $this->assertEquals(
            [
                'ALTER TABLE `testTable` CHANGE `foo` `foo` INT DEFAULT NULL',
                'ALTER TABLE `testTable` DROP COLUMN `foo`',
                'ALTER TABLE `testTable` ADD COLUMN `foo` INT DEFAULT NULL',
            ],
            $this->sqlMigrationBuilder->getMigrationSqlQueries(
                $sourceSchema,
                $destinationSchema
            )
        );
    }

    /**
     * {@inherit}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->sqlMigrationBuilder = new MigrationQueriesBuilder();

        $this->schemaBuilder = new SchemaBuilder();
    }
}
