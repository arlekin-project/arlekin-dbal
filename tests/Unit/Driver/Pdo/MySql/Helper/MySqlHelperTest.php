<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Helper;

use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\ColumnDataType;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\Index;
use Calam\Dbal\Driver\Pdo\MySql\Element\IndexTypes;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper;
use Calam\Dbal\Tests\BaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class MySqlHelperTest extends BaseTest
{
    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::betweenParentheses
     */
    public function testBetweenParentheses()
    {
        $this->assertSame(
            '(test)',
            MySqlHelper::betweenParentheses('test')
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::generateSqlCollection
     */
    public function testGenerateSqlCollection()
    {
        $this->assertSame(
            'test, test1',
            MySqlHelper::generateSqlCollection(
                [
                    'test',
                    'test1',
                ]
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::generateSqlCollectionBetweenParentheses
     */
    public function testGenerateSqlCollectionBetweenParentheses()
    {
        $this->assertSame(
            '(test, test1)',
            MySqlHelper::generateSqlCollectionBetweenParentheses(
                [
                    'test',
                    'test1',
                ]
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::wrapString
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::doWrap
     */
    public function testWrapString()
    {
        $this->assertSame(
            "'test'",
            MySqlHelper::wrapString('test')
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::wrapStringCollection
     */
    public function testWrapStringCollection()
    {
        $this->assertSame(
            [
                "'test'",
                "'test1'",
            ],
            MySqlHelper::wrapStringCollection(
                [
                    'test',
                    'test1',
                ]
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::backquoteTableOrColumnName
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::doWrap
     */
    public function testBackquoteTableOrColumnName()
    {
        $this->assertSame(
            '`test`',
            MySqlHelper::backquoteTableOrColumnName('test')
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::backquoteArrayOfTableOrColumnNames
     */
    public function testBackquoteArrayOfTableOrColumnNames()
    {
        $this->assertSame(
            [
                '`test`',
                '`test1`',
            ],
            MySqlHelper::backquoteArrayOfTableOrColumnNames(
                [
                    'test',
                    'test1',
                ]
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::getForeignKeyUniqueNameFromForeignKey
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::getForeignKeyUniqueStringIdFromForeignKeyAsArray
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::getForeignKeyUniqStringIdFromForeignKey
     */
    public function testGetForeignKeyUniqueNameFromForeignKey()
    {
        $this->assertSame(
            'fk_d80ae8083ad72f784e2008c1ab87ee6d79e583ef',
            MySqlHelper::getForeignKeyUniqueNameFromForeignKey(
                $this->getBasicForeignKey()
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::getForeignKeyUniqueNameFromForeignKeyAsArray
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::getForeignKeyUniqueStringIdFromForeignKeyAsArray
     */
    public function testGetForeignKeyUniqueNameFromForeignKeyAsArray()
    {
        $this->assertSame(
            'fk_d80ae8083ad72f784e2008c1ab87ee6d79e583ef',
            MySqlHelper::getForeignKeyUniqueNameFromForeignKeyAsArray(
                $this->getBasicForeignKeyAsArray()
            )
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::generateColumnSql
     */
    public function testGenerateColumnSql()
    {
        $column = new Column();

        $exceptionThrown = false;

        try {
            MySqlHelper::generateColumnSql($column);
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
            MySqlHelper::generateColumnSql($column);
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
            MySqlHelper::generateColumnSql($column);
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
            MySqlHelper::generateColumnSql($column);
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
            MySqlHelper::generateColumnSql($column);
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
            MySqlHelper::generateColumnSql($column)
        );

        $column->setNullable(false);

        $this->assertSame(
            "`testColumn` ENUM('a', 'b') NOT NULL",
            MySqlHelper::generateColumnSql($column)
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
            MySqlHelper::generateColumnSql($column)
        );

        $column->setParameter('unsigned', true);

        $this->assertSame(
            '`testColumn` INT(11) UNSIGNED NOT NULL',
            MySqlHelper::generateColumnSql($column)
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::generateCreateTableColumnsSql
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
            MySqlHelper::generateCreateTableColumnsSql($columns)
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::generateCreateAlterTableCreateColumnSql
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
            MySqlHelper::generateCreateAlterTableCreateColumnSql($column)
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::generateCreateAlterTableCreateIndexSql
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
            MySqlHelper::generateCreateAlterTableCreateIndexSql($index);
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
            MySqlHelper::generateCreateAlterTableCreateIndexSql(
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
            MySqlHelper::generateCreateAlterTableCreateIndexSql($index);
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
            MySqlHelper::generateCreateAlterTableCreateIndexSql($index)
        );

        $index->setType(
            IndexTypes::BTREE
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD INDEX `testIndex` (`col1`) USING BTREE',
            MySqlHelper::generateCreateAlterTableCreateIndexSql($index)
        );

        $index->setType(
            IndexTypes::HASH
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD INDEX `testIndex` (`col1`) USING HASH',
            MySqlHelper::generateCreateAlterTableCreateIndexSql($index)
        );

        $index->setType(
            IndexTypes::KIND_UNIQUE
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD UNIQUE INDEX `testIndex` (`col1`)',
            MySqlHelper::generateCreateAlterTableCreateIndexSql($index)
        );

        $index->setType(
            IndexTypes::KIND_FULLTEXT
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD INDEX FULLTEXT `testIndex` (`col1`)',
            MySqlHelper::generateCreateAlterTableCreateIndexSql($index)
        );

        $index->setType(
            IndexTypes::KIND_SPATIAL
        );

        $this->assertSame(
            'ALTER TABLE `testTable` ADD INDEX SPATIAL `testIndex` (`col1`)',
            MySqlHelper::generateCreateAlterTableCreateIndexSql($index)
        );
    }

    /**
    * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::generateAlterTableSetAutoIncrementSqlQuery
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
            MySqlHelper::generateAlterTableSetAutoIncrementSqlQuery(
                $destinationColumn,
                $destinationTable
            )
        );
    }

    /**
    * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::generateAlterTableUnsetAutoIncrementSqlQuery
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
            MySqlHelper::generateAlterTableUnsetAutoIncrementSqlQuery(
                $destinationColumn,
                $destinationTable
            )
        );
    }

    /**
    * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::generateMySqlStartTransactionQuery
    */
    public function testGenerateMySqlStartTransactionQuery()
    {
        $this->assertSame(
            'START TRANSACTION',
            MySqlHelper::generateMySqlStartTransactionQuery()
        );
    }

    /**
    * @covers Calam\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper::generateMySqlCommitQuery
    */
    public function testGenerateMySqlCommitQuery()
    {
        $this->assertSame(
            'COMMIT',
            MySqlHelper::generateMySqlCommitQuery()
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

        $result = MySqlHelper::columnsAreSameIgnoreAutoIncrement($initColumn1, $initColumn2);

        $this->assertTrue($result);

        $column1 = clone $initColumn1;
        $column2 = clone $initColumn2;

        $column1->setAutoIncrementable(true);
        $column2->setAutoIncrementable(false);

        $result2 = MySqlHelper::columnsAreSameIgnoreAutoIncrement($column1, $column2);

        $this->assertTrue($result2);

        $column1 = clone $initColumn1;
        $column2 = clone $initColumn2;

        $column1->setName('test');
        $column2->setName('test2');

        $result3 = MySqlHelper::columnsAreSameIgnoreAutoIncrement($column1, $column2);

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
