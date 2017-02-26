<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\ColumnDataTypes;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Tests\BaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class TableTest extends BaseTest
{
    /**
     * @covers Table::__construct
     */
    public function testConstruct()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $this->assertAttributeSame('foo', 'name', $table);
        $this->assertAttributeSame([ $column ], 'columns', $table);
    }

    /**
     * @covers Table::getName
     */
    public function testGetAndSetName()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $this->assertSame('foo', $table->getName());
    }

    /**
     * @covers Table::getColumns
     */
    public function testGetColumns()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $this->assertSame([ $column ], $table->getColumns());
    }

//
//    /**
//     * @covers Calam\Dbal\SqlBased\Element\Table::hasForeignKeyWithColumnsAndReferencedColumnsNamed
//     */
//    public function testHasForeignKeyWithColumnsAndReferencedColumnsNamed()
//    {
//        $testTable = new Table();
//
//        $testTable->setName('test');
//
//        $this->assertFalse(
//            $testTable->hasForeignKeyWithColumnsAndReferencedColumnsNamed(
//                [
//                    'testColumnInTest',
//                ],
//                'test2',
//                [
//                    'testColumnInTest2',
//                ]
//            )
//        );
//
//        $testTable2 = new Table();
//
//        $testTable2->setName('test2');
//
//        $testColumnInTest = new Column();
//
//        $testColumnInTest->setName('testColumnInTest');
//
//        $testColumnInTest2 = new Column();
//
//        $testColumnInTest2->setName('testColumnInTest2');
//
//        $testTable->setColumns(
//            [
//                $testColumnInTest,
//            ]
//        );
//
//        $testTable2->setColumns(
//            [
//                $testColumnInTest2,
//            ]
//        );
//
//        $testForeignKey = new ForeignKey();
//
//        $testForeignKey->setColumns(
//            [
//                $testColumnInTest,
//            ]
//        );
//
//        $testForeignKey->setReferencedColumns(
//            [
//                $testColumnInTest2,
//            ]
//        );
//
//        $testForeignKey->setReferencedTable($testTable2);
//
//        $testTable->setForeignKeys(
//            [
//                $testForeignKey,
//            ]
//        );
//
//        $this->assertTrue(
//            $testTable->hasForeignKeyWithColumnsAndReferencedColumnsNamed(
//                [
//                    'testColumnInTest',
//                ],
//                'test2',
//                [
//                    'testColumnInTest2',
//                ]
//            )
//        );
//    }
//
//    /**
//     * @covers Calam\Dbal\SqlBased\Element\Table::hasColumn
//     */
//    public function testHasColumn()
//    {
//        $table = new Table();
//
//        $column = new Column();
//
//        $this->assertFalse(
//            $table->hasColumn($column)
//        );
//
//        $table->setColumns(
//            [
//                $column,
//            ]
//        );
//
//        $this->assertTrue(
//            $table->hasColumn($column)
//        );
//    }
//
//    /**
//     * @covers Calam\Dbal\SqlBased\Element\Table::hasColumnWithName
//     */
//    public function testHasColumnWithName()
//    {
//        $table = new Table();
//
//        $column = new Column();
//
//        $column->setName('test');
//
//        $this->assertFalse(
//            $table->hasColumnWithName('test')
//        );
//
//        $table->setColumns(
//            [
//                $column,
//            ]
//        );
//
//        $this->assertTrue(
//            $table->hasColumnWithName('test')
//        );
//    }
//
//    /**
//     * @covers Calam\Dbal\SqlBased\Element\Table::hasIndexWithName
//     */
//    public function testHasIndexWithName()
//    {
//        $table = new Table();
//
//        $index = new Index();
//
//        $index->setName('test');
//
//        $this->assertFalse(
//            $table->hasIndexWithName('test')
//        );
//
//        $table->setIndexes(
//            [
//                $index,
//            ]
//        );
//
//        $this->assertTrue(
//            $table->hasIndexWithName('test')
//        );
//    }
//
//    /**
//     * @covers Calam\Dbal\SqlBased\Element\Table::hasPrimaryKeyWithColumnsNamed
//     */
//    public function testHasPrimaryKeyWithColumnsNamed()
//    {
//        $table = new Table();
//
//        $column = new Column();
//        $column2 = new Column();
//
//        $column->setName('test');
//        $column2->setName('test2');
//
//        $primaryKey = new PrimaryKey();
//
//        $primaryKey->setColumns(
//            [
//                $column,
//                $column2,
//            ]
//        );
//
//        $this->assertFalse(
//            $table->hasPrimaryKeyWithColumnsNamed(
//                [
//                    'test',
//                    'test2',
//                ]
//            )
//        );
//
//        $table->setPrimaryKey($primaryKey);
//
//        $this->assertTrue(
//            $table->hasPrimaryKeyWithColumnsNamed(
//                [
//                    'test',
//                    'test2',
//                ]
//            )
//        );
//    }
//
//    /**
//     * @covers Calam\Dbal\SqlBased\Element\Table::getIndexWithName
//     */
//    public function testGetIndexWithName()
//    {
//        $table = new Table();
//
//        $table->setName('testTable');
//
//        $index = new Index();
//
//        $index->setName('test');
//
//        $table->setIndexes(
//            [
//                $index,
//            ]
//        );
//
//        $result = $table->getIndexWithName('test');
//
//        $this->assertSame($index, $result);
//    }
//
//    /**
//     * @covers Calam\Dbal\SqlBased\Element\Table::getIndexWithName
//     */
//    public function testGetIndexWithNameExceptionIfTableHasNoIndexWithName()
//    {
//        $table = new Table();
//
//        $table->setName('testTable');
//
//        CommonTestHelper::assertExceptionThrown(
//            function () use ($table) {
//                $table->getIndexWithName('test');
//            },
//            \Exception::class,
//            'Table "testTable" has no index with name "test".'
//        );
//    }
//
//    /**
//     * @covers Calam\Dbal\SqlBased\Element\Table::getColumnWithName
//     */
//    public function testGetColumnWithName()
//    {
//        $table = new Table();
//
//        $table->setName('testTable');
//
//        $column = new Column();
//
//        $column->setName('test');
//
//        $table->setColumns(
//            [
//                $column,
//            ]
//        );
//
//        $result = $table->getColumnWithName('test');
//
//        $this->assertSame($column, $result);
//    }
//
//    /**
//     * @covers Calam\Dbal\SqlBased\Element\Table::getColumnWithName
//     */
//    public function testGetColumnWithNameExceptionIfTableHasNoColumnWithName()
//    {
//        $table = new Table();
//
//        $table->setName('testTable');
//
//        CommonTestHelper::assertExceptionThrown(
//            function () use ($table) {
//                $table->getColumnWithName('test');
//            },
//            \Exception::class,
//            'Table "testTable" has no column with name "test".'
//        );
//    }
//
//    /**
//     * @return Column
//     */
//    protected function createBaseNewColumn()
//    {
//        return $this->getMockForAbstractClass(
//            Column::class
//        );
//    }
//
//    /**
//     * @return Index
//     */
//    protected function createBaseNewIndex()
//    {
//        return $this->getMockForAbstractClass(
//            Index::class
//        );
//    }
//
//    /**
//     * @return ForeignKey
//     */
//    protected function createBaseNewForeignKey()
//    {
//        return $this->getMockForAbstractClass(
//            ForeignKey::class
//        );
//    }
//
//    /**
//     * @return PrimaryKey
//     */
//    protected function createBaseNewPrimaryKey()
//    {
//        return $this->getMockForAbstractClass(
//            PrimaryKey::class
//        );
//    }
//
//    /**
//     * @return Table
//     */
//    protected function createBaseNewTable()
//    {
//        return $this->getMockForAbstractClass(
//            Table::class
//        );
//    }
}
