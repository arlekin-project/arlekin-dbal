<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Util;

use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\ColumnDataType;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\Index;
use Calam\Dbal\Driver\Pdo\MySql\Element\IndexTypes;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Driver\Pdo\MySql\Util\Util;
use Calam\Dbal\Tests\BaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class UtilTest extends BaseTest
{
    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::betweenParentheses
     */
    public function testBetweenParentheses()
    {
        $this->assertSame(
            '(test)',
            Util::betweenParentheses('test')
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::generateSqlCollection
     */
    public function testGenerateSqlCollection()
    {
        $this->assertSame(
            'test, test1',
            Util::generateSqlCollection(
                [
                    'test',
                    'test1',
                ]
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::generateSqlCollectionBetweenParentheses
     */
    public function testGenerateSqlCollectionBetweenParentheses()
    {
        $this->assertSame(
            '(test, test1)',
            Util::generateSqlCollectionBetweenParentheses(
                [
                    'test',
                    'test1',
                ]
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::wrapString
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::doWrap
     */
    public function testWrapString()
    {
        $this->assertSame(
            "'test'",
            Util::wrapString('test')
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::wrapStringCollection
     */
    public function testWrapStringCollection()
    {
        $this->assertSame(
            [
                "'test'",
                "'test1'",
            ],
            Util::wrapStringCollection(
                [
                    'test',
                    'test1',
                ]
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::backquoteTableOrColumnName
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::doWrap
     */
    public function testBackquoteTableOrColumnName()
    {
        $this->assertSame(
            '`test`',
            Util::backquoteTableOrColumnName('test')
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::backquoteArrayOfTableOrColumnNames
     */
    public function testBackquoteArrayOfTableOrColumnNames()
    {
        $this->assertSame(
            [
                '`test`',
                '`test1`',
            ],
            Util::backquoteArrayOfTableOrColumnNames(
                [
                    'test',
                    'test1',
                ]
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::getForeignKeyUniqueNameFromForeignKey
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::getForeignKeyUniqueStringIdFromForeignKeyAsArray
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::getForeignKeyUniqStringIdFromForeignKey
     */
    public function testGetForeignKeyUniqueNameFromForeignKey()
    {
        $this->assertSame(
            'fk_d80ae8083ad72f784e2008c1ab87ee6d79e583ef',
            Util::getForeignKeyUniqueNameFromForeignKey(
                $this->getBasicForeignKey()
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::getForeignKeyUniqueNameFromForeignKeyAsArray
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::getForeignKeyUniqueStringIdFromForeignKeyAsArray
     */
    public function testGetForeignKeyUniqueNameFromForeignKeyAsArray()
    {
        $this->assertSame(
            'fk_d80ae8083ad72f784e2008c1ab87ee6d79e583ef',
            Util::getForeignKeyUniqueNameFromForeignKeyAsArray(
                $this->getBasicForeignKeyAsArray()
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::generateColumnSql
     */
    public function testGenerateColumnSql()
    {
        $column = new Column();

        $exceptionThrown = false;

        try {
            Util::generateColumnSql($column);
        } catch (\Exception $ex) {
            $exceptionThrown = true;

            $this->assertSame(
                'A table is required to generate column SQL.',
                $ex->getMessage()
            );
        }

        $this->assertTrue($exceptionThrown);

        $table = new Table();

        $table->setName('testTable');

        $column->setTable($table);

        $exceptionThrown = false;

        try {
            Util::generateColumnSql($column);
        } catch (\Exception $ex) {
            $exceptionThrown = true;

            $this->assertSame(
                'A column name is required for column from table "testTable".',
                $ex->getMessage()
            );
        }

        $this->assertTrue($exceptionThrown);

        $column->setName('testColumn');

        $exceptionThrown = false;

        try {
            Util::generateColumnSql($column);
        } catch (\Exception $ex) {
            $exceptionThrown = true;

            $this->assertSame(
                'A column data type is required for column "testColumn" from table "testTable".',
                $ex->getMessage()
            );
        }

        $this->assertTrue($exceptionThrown);

        $column->setDataType(ColumnDataType::TYPE_ENUM);

        $exceptionThrown = false;

        try {
            Util::generateColumnSql($column);
        } catch (\Exception $ex) {
            $exceptionThrown = true;

            $this->assertSame(
                'Nullable is required for column "testColumn" from table "testTable".',
                $ex->getMessage()
            );
        }

        $this->assertTrue($exceptionThrown);

        $column->setNullable(true);

        $exceptionThrown = false;

        try {
            Util::generateColumnSql($column);
        } catch (\Exception $ex) {
            $exceptionThrown = true;

            $this->assertSame(
                'Parameter allowedValues is required.',
                $ex->getMessage()
            );
        }

        $this->assertTrue(
            $exceptionThrown
        );

        $column->setParameters(
            [
                'allowedValues' => [
                    'a',
                    'b',
                ],
            ]
        );

        $this->assertSame(
            "`testColumn` ENUM('a', 'b') DEFAULT NULL",
            Util::generateColumnSql($column)
        );

        $column->setNullable(false);

        $this->assertSame(
            "`testColumn` ENUM('a', 'b') NOT NULL",
            Util::generateColumnSql($column)
        );

        $column->setDataType(
            ColumnDataType::TYPE_INT
        )->setParameters(
            [
                'length' => 11,
            ]
        );

        $this->assertSame(
            '`testColumn` INT(11) NOT NULL',
            Util::generateColumnSql($column)
        );

        $column->setParameter('unsigned', true);

        $this->assertSame(
            '`testColumn` INT(11) UNSIGNED NOT NULL',
            Util::generateColumnSql($column)
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::generateCreateTableColumnsSql
     */
    public function testGenerateCreateTableColumnsSql()
    {
        $table = new Table();

        $table->setName('testTable');

        $column1 = new Column();
        $column2 = new Column();

        $column1->setTable(
            $table
        )->setNullable(
            false
        )->setDataType(
            ColumnDataType::TYPE_INT
        )->setName(
            'col1'
        );

        $column2->setTable(
            $table
        )->setNullable(
            true
        )->setDataType(
            ColumnDataType::TYPE_VARCHAR
        )->setName(
            'col2'
        );

        $columns = [
            $column1,
            $column2,
        ];

        $this->assertSame(
            '(`col1` INT NOT NULL, `col2` VARCHAR DEFAULT NULL)',
            Util::generateCreateTableColumnsSql($columns)
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::generateCreateAlterTableCreateColumnSql
     */
    public function testGenerateCreateAlterTableCreateColumnSql()
    {
        $table = new Table();

        $table->setName('testTable');

        $column = new Column();

        $column->setTable(
            $table
        )->setNullable(
            false
        )->setDataType(
            ColumnDataType::TYPE_INT
        )->setName(
            'col1'
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD COLUMN `col1` INT NOT NULL',
            Util::generateCreateAlterTableCreateColumnSql($column)
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::generateCreateAlterTableCreateIndexSql
     */
    public function testGenerateCreateAlterTableCreateIndexSql()
    {
        $table = new Table();

        $column = new Column();

        $column->setTable(
            $table
        )->setNullable(
            false
        )->setDataType(
            ColumnDataType::TYPE_INT
        )->setName(
            'col1'
        );

        $index = new Index();

        $exceptionThrown = false;

        try {
            Util::generateCreateAlterTableCreateIndexSql($index);
        } catch (\Exception $ex) {
            $exceptionThrown = true;

            $this->assertSame(
                'A table is required to generate column SQL.',
                $ex->getMessage()
            );
        }

        $this->assertTrue(
            $exceptionThrown
        );

        $index->setTable($table);

        $table->setName('testTable');

        $exceptionThrown = false;

        try {
            Util::generateCreateAlterTableCreateIndexSql(
                $index
            );
        } catch (\Exception $ex) {
            $exceptionThrown = true;

            $this->assertSame(
                'An index name is required for index from table "testTable".',
                $ex->getMessage()
            );
        }

        $this->assertTrue($exceptionThrown);

        $index->setName('testIndex');

        $exceptionThrown = false;

        try {
            Util::generateCreateAlterTableCreateIndexSql($index);
        } catch (\Exception $ex) {
            $exceptionThrown = true;

            $this->assertSame(
                'Index "testIndex" from table "testTable" requires at least one column.',
                $ex->getMessage()
            );
        }

        $this->assertTrue($exceptionThrown);

        $index->setColumns(
            [
                $column,
            ]
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD INDEX `testIndex` (`col1`)',
            Util::generateCreateAlterTableCreateIndexSql($index)
        );

        $index->setType(
            IndexTypes::BTREE
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD INDEX `testIndex` (`col1`) USING BTREE',
            Util::generateCreateAlterTableCreateIndexSql($index)
        );

        $index->setType(
            IndexTypes::HASH
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD INDEX `testIndex` (`col1`) USING HASH',
            Util::generateCreateAlterTableCreateIndexSql($index)
        );

        $index->setType(
            IndexTypes::KIND_UNIQUE
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD UNIQUE INDEX `testIndex` (`col1`)',
            Util::generateCreateAlterTableCreateIndexSql($index)
        );

        $index->setType(
            IndexTypes::KIND_FULLTEXT
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD INDEX FULLTEXT `testIndex` (`col1`)',
            Util::generateCreateAlterTableCreateIndexSql($index)
        );

        $index->setType(
            IndexTypes::KIND_SPATIAL
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD INDEX SPATIAL `testIndex` (`col1`)',
            Util::generateCreateAlterTableCreateIndexSql($index)
        );
    }

    /**
    * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::generateAlterTableSetAutoIncrementSqlQuery
    */
    public function testGenerateAlterTableSetAutoIncrementSqlQuery()
    {
        $destinationColumn = new Column();

        $destinationColumn->setName(
            'testColumn'
        )->setNullable(
            false
        )->setDataType(
            ColumnDataType::TYPE_INT
        );

        $destinationTable = new Table();

        $destinationTable->setName(
            'testTable'
        )->addColumn(
            $destinationColumn
        );

        $this->assertSame(
            'ALTER TABLE `testTable` CHANGE `testColumn` `testColumn` INT NOT NULL AUTO_INCREMENT',
            Util::generateAlterTableSetAutoIncrementSqlQuery(
                $destinationColumn,
                $destinationTable
            )
        );
    }

    /**
    * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::generateAlterTableUnsetAutoIncrementSqlQuery
    */
    public function testGenerateAlterTableUnsetAutoIncrementSqlQuery()
    {
        $destinationColumn = new Column();

        $destinationColumn->setName(
            'testColumn'
        )->setNullable(
            false
        )->setDataType(
            ColumnDataType::TYPE_INT
        );

        $destinationTable = new Table();

        $destinationTable->setName(
            'testTable'
        )->addColumn(
            $destinationColumn
        );

        $this->assertSame(
            'ALTER TABLE `testTable` CHANGE `testColumn` `testColumn` INT NOT NULL',
            Util::generateAlterTableUnsetAutoIncrementSqlQuery(
                $destinationColumn,
                $destinationTable
            )
        );
    }

    /**
    * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::generateMySqlStartTransactionQuery
    */
    public function testGenerateMySqlStartTransactionQuery()
    {
        $this->assertSame(
            'START TRANSACTION',
            Util::generateMySqlStartTransactionQuery()
        );
    }

    /**
    * @covers Calam\Dbal\Driver\Pdo\MySql\Util\Util::generateMySqlCommitQuery
    */
    public function testGenerateMySqlCommitQuery()
    {
        $this->assertSame(
            'COMMIT',
            Util::generateMySqlCommitQuery()
        );
    }
    
    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::columnsAreSameIgnoreAutoIncrement
     */
    public function testColumnsAreSameExceptAutoIncrement()
    {
        $initColumn1 = new Column();
        $initColumn2 = new Column();
        $table = new Table();

        $table->setColumns(
            [
                $initColumn1,
                $initColumn2,
            ]
        );

        $table->setName('testTable');

        $initColumn1->setDataType(ColumnDataType::TYPE_INT);
        $initColumn2->setDataType(ColumnDataType::TYPE_INT);

        $result = Util::columnsAreSameIgnoreAutoIncrement($initColumn1, $initColumn2);

        $this->assertTrue($result);

        $column1 = clone $initColumn1;
        $column2 = clone $initColumn2;

        $column1->setAutoIncrementable(true);
        $column2->setAutoIncrementable(false);

        $result2 = Util::columnsAreSameIgnoreAutoIncrement($column1, $column2);

        $this->assertTrue($result2);

        $column1 = clone $initColumn1;
        $column2 = clone $initColumn2;

        $column1->setName('test');
        $column2->setName('test2');

        $result3 = Util::columnsAreSameIgnoreAutoIncrement($column1, $column2);

        $this->assertFalse($result3);
    }

    /**
     * @return ForeignKey
     */
    protected function getBasicForeignKey()
    {
        $table = new Table();

        $table->setName('testTableName');

        $referencedTable = new Table();

        $referencedTable->setName('referencedTableName');

        $column = new Column();

        $column->setName('testColumn');

        $referencedColumn = new Column();

        $referencedColumn->setName(
            'testReferencedColumn'
        );

        $columns = [
            $column,
        ];

        $referencedColumns = [
            $referencedColumn,
        ];

        $foreignKey = new ForeignKey();

        $foreignKey->setTable(
            $table
        )->setReferencedTable(
            $referencedTable
        )->setColumns(
            $columns
        )->setReferencedColumns(
            $referencedColumns
        );

        return $foreignKey;
    }

    protected function getBasicForeignKeyAsArray()
    {
        $foreignKey = $this->getBasicForeignKey();

        $arr = $foreignKey->toArray();

        return $arr;
    }
}
