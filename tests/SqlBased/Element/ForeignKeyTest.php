<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\SqlBased\Element;

use Arlekin\Dbal\SqlBased\Element\Column;
use Arlekin\Dbal\SqlBased\Element\ForeignKey;
use Arlekin\Dbal\SqlBased\Element\Table;
use Arlekin\Dbal\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ForeignKeyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::__construct
     */
    public function testConstruct()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $this->assertAttributeSame(null, 'table', $foreignKey);
        $this->assertAttributeSame([], 'columns', $foreignKey);
        $this->assertAttributeSame(null, 'referencedTable', $foreignKey);
        $this->assertAttributeSame([], 'referencedColumns', $foreignKey);
        $this->assertAttributeSame(null, 'onDelete', $foreignKey);
        $this->assertAttributeSame(null, 'onUpdate', $foreignKey);
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::getTable
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::setTable
     */
    public function testGetAndSetTable()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewForeignKey(),
            'table',
            $this->createBaseNewTable()
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::getColumns
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::setColumns
     */
    public function testGetAndSetColumns()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewForeignKey(),
            'columns',
            [
                $this->createBaseNewColumn(),
            ]
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::getReferencedTable
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::setReferencedTable
     */
    public function testGetAndSetReferencedTable()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewForeignKey(),
            'referencedTable',
            $this->createBaseNewTable()
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::getReferencedColumns
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::setReferencedColumns
     */
    public function testGetAndSetReferencedColumns()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewForeignKey(),
            'referencedcolumns',
            [
                $this->createBaseNewColumn(),
            ]
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::getOnDelete
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::setOnDelete
     */
    public function testGetAndSetOnDelete()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewForeignKey(),
            'onDelete',
            $this->createBaseNewTable()
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::getOnUpdate
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::setOnUpdate
     */
    public function testGetAndSetOnUpdate()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewForeignKey(),
            'onUpdate',
            $this->createBaseNewTable()
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\ForeignKey::toArray
     */
    public function testToArray()
    {
        $foreignKey = $this->createBaseTestForeignKey();

        $arr = $foreignKey->toArray();

        $expected = [
            'table' => 'testTable',
            'columns' => [
                'deptNo',
            ],
            'referencedTable' => 'departments',
            'referencedColumns' => [
                'deptNo1',
            ],
            'onDelete' => null,
            'onUpdate' => null,
        ];

        $this->assertEquals($expected, $arr);
    }

    /**
     * @return ForeignKey
     */
    protected function createBaseTestForeignKey()
    {
        $table = $this->createBaseNewTable();

        $table->setName('testTable');

        $referencedTable = $this->createBaseNewTable();

        $referencedTable->setName('departments');

        $column = $this->createBaseNewColumn();

        $column->setName(
            'deptNo'
        )->setTable(
            $table
        );

        $referencedColumn = $this->createBaseNewColumn();

        $referencedColumn->setName(
            'deptNo1'
        )->setTable(
            $referencedTable
        );

        $foreignKey = $this->createBaseNewForeignKey();

        $foreignKey->addColumn(
            $column
        )->addReferencedColumn(
            $referencedColumn
        )->setReferencedTable(
            $referencedTable
        )->setTable(
            $table
        );

        return $foreignKey;
    }

    /**
     * @return Table
     */
    protected function createBaseNewTable()
    {
        return $this->getMockForAbstractClass(
            Table::class
        );
    }

    /**
     * @return Column
     */
    protected function createBaseNewColumn()
    {
        return $this->getMockForAbstractClass(
            Column::class
        );
    }

    /**
     * @return ForeignKey
     */
    protected function createBaseNewForeignKey()
    {
        return $this->getMockForAbstractClass(
            ForeignKey::class
        );
    }
}
