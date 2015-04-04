<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\Tests\SqlBased\Element;

use Arlecchino\Core\Collection\ArrayCollection;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Column;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table;
use Arlecchino\Core\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ForeignKeyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::__construct
     */
    public function testConstruct()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $this->assertAttributeSame(
            null,
            'table',
            $foreignKey
        );
        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'columns',
            $foreignKey
        );
        $this->assertAttributeSame(
            null,
            'referencedTable',
            $foreignKey
        );
        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'referencedColumns',
            $foreignKey
        );
        $this->assertAttributeSame(
            null,
            'onDelete',
            $foreignKey
        );
        $this->assertAttributeSame(
            null,
            'onUpdate',
            $foreignKey
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::getTable
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::setTable
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
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::getColumns
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::setColumns
     */
    public function testGetAndSetColumns()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewForeignKey(),
            'columns',
            array(
                $this->createBaseNewColumn()
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::addColumn
     */
    public function testAddColumn()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $foreignKey->setColumns(
            array(
               $this->createBaseNewColumn()
            )
        );

        CommonTestHelper::testBasicAddForProperty(
            $this,
            $foreignKey,
            'columns',
            $this->createBaseNewColumn()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::addColumns
     */
    public function testAddColumns()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $foreignKey->setColumns(
            array(
               $this->createBaseNewColumn()
            )
        );

        CommonTestHelper::testBasicAddCollectionForProperty(
            $this,
            $foreignKey,
            'columns',
            array(
                $this->createBaseNewColumn()
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::getReferencedTable
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::setReferencedTable
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
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::getReferencedColumns
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::setReferencedColumns
     */
    public function testGetAndSetReferencedColumns()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewForeignKey(),
            'referencedcolumns',
            array(
                $this->createBaseNewColumn()
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::addReferencedColumn
     */
    public function testAddReferencedColumn()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $foreignKey->setReferencedColumns(
            array(
               $this->createBaseNewColumn()
            )
        );

        CommonTestHelper::testBasicAddForProperty(
            $this,
            $foreignKey,
            'referencedColumns',
            $this->createBaseNewColumn()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::addReferencedColumns
     */
    public function testAddReferencedColumns()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $foreignKey->setReferencedColumns(
            array(
               $this->createBaseNewColumn()
            )
        );

        CommonTestHelper::testBasicAddCollectionForProperty(
            $this,
            $foreignKey,
            'referencedColumns',
            array(
                $this->createBaseNewColumn()
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::getOnDelete
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::setOnDelete
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
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::getOnUpdate
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::setOnUpdate
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
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey::toArray
     */
    public function testToArray()
    {
        $foreignKey = $this->createBaseTestForeignKey();

        $arr = $foreignKey->toArray();
        $expected = array(
            'table' => 'testTable',
            'columns' => array(
                'deptNo'
            ),
            'referencedTable' => 'departments',
            'referencedColumns' => array(
                'deptNo1'
            ),
            'onDelete' => null,
            'onUpdate' => null
        );

        $this->assertEquals(
            $expected,
            $arr
        );
    }

    /**
     * @return ForeignKey
     */
    protected function createBaseTestForeignKey()
    {
        $table = $this->createBaseNewTable();
        $table->setName(
            'testTable'
        );

        $referencedTable = $this->createBaseNewTable();
        $referencedTable->setName(
            'departments'
        );

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
