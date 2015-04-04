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
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table;
use Arlecchino\Core\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class TableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::__construct
     */
    public function testConstruct()
    {
        $table = $this->createBaseNewTable();

        $this->assertAttributeSame(
            null,
            'name',
            $table
        );
        $this->assertAttributeSame(
            null,
            'primaryKey',
            $table
        );
        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'columns',
            $table
        );
        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'foreignKeys',
            $table
        );
        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'indexes',
            $table
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::getName
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::setName
     */
    public function testGetAndSetName()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewTable(),
            'name',
            uniqid(
                'test_name_',
                true
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::getPrimaryKey
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::setPrimaryKey
     */
    public function testGetAndSetPrimaryKey()
    {
        $primaryKey = $this->createBaseNewPrimaryKey();

        $table = $this->createBaseNewTable();

        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $table,
            'primaryKey',
            $primaryKey
        );

        $this->assertSame(
            $table,
            $primaryKey->getTable()
        );

        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $table,
            'primaryKey',
            null
        );

        $this->assertNull(
            $primaryKey->getTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::getColumns
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::setColumns
     */
    public function testGetAndSetColumns()
    {
        $column = $this->createBaseNewColumn();

        $table = $this->createBaseNewTable();

        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $table,
            'columns',
            array(
                $column
            )
        );

        $this->assertSame(
            $table,
            $column->getTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::addColumn
     */
    public function testAddColumn()
    {
        $column = $this->createBaseNewColumn();

        $table = $this->createBaseNewTable();

        $table->setColumns(
            array(
                $this->createBaseNewColumn()
            )
        );

        CommonTestHelper::testBasicAddForProperty(
            $this,
            $table,
            'columns',
            $column
        );

        $this->assertSame(
            $table,
            $column->getTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::addColumns
     */
    public function testAddColumns()
    {
        $column = $this->createBaseNewColumn();

        $table = $this->createBaseNewTable();

        $table->setColumns(
            array(
                $this->createBaseNewColumn()
            )
        );

        CommonTestHelper::testBasicAddCollectionForProperty(
            $this,
            $table,
            'columns',
            array(
                $column
            )
        );

        $this->assertSame(
            $table,
            $column->getTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::getForeignKeys
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::setForeignKeys
     */
    public function testGetAndSetForeignKeys()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $table = $this->createBaseNewTable();

        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $table,
            'foreignKeys',
            array(
                $foreignKey
            )
        );

        $this->assertEquals(
            $table,
            $foreignKey->getTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::addForeignKey
     */
    public function testAddForeignKey()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $table = $this->createBaseNewTable();

        $table->setForeignKeys(
            array(
                $this->createBaseNewForeignKey()
            )
        );

        CommonTestHelper::testBasicAddForProperty(
            $this,
            $table,
            'foreignKeys',
            $foreignKey
        );

        $this->assertEquals(
            $table,
            $foreignKey->getTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::addForeignKeys
     */
    public function testAddForeignKeys()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $table = $this->createBaseNewTable();

        $table->setForeignKeys(
            array(
                $this->createBaseNewForeignKey()
            )
        );

        CommonTestHelper::testBasicAddCollectionForProperty(
            $this,
            $table,
            'foreignKeys',
            array(
                $foreignKey
            )
        );

        $this->assertEquals(
            $table,
            $foreignKey->getTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::getIndexes
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::setIndexes
     */
    public function testGetAndSetIndexes()
    {
        $index = $this->createBaseNewIndex();

        $table = $this->createBaseNewTable();

        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $table,
            'indexes',
            array(
                $index
            )
        );

        $this->assertSame(
            $table,
            $index->getTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::addIndex
     */
    public function testAddIndex()
    {
        $index = $this->createBaseNewIndex();

        $table = $this->createBaseNewTable();

        CommonTestHelper::testBasicAddForProperty(
            $this,
            $table,
            'indexes',
            $index,
            'index'
        );

        $this->assertSame(
            $table,
            $index->getTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::addIndexes
     */
    public function testAddIndexes()
    {
        $index = $this->createBaseNewIndex();

        $table = $this->createBaseNewTable();

        CommonTestHelper::testBasicAddCollectionForProperty(
            $this,
            $table,
            'indexes',
            array(
                $index
            )
        );

        $this->assertSame(
            $table,
            $index->getTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table::toArray
     */
    public function testToArray()
    {
        $table2 = $this->createBaseNewTable();
        $table2->setName(
            'table2'
        );
        $table2IdColumn = $this->createBaseNewColumn();
        $table2IdColumn->setName('id');
        $table2->setColumns(
            array(
                $table2IdColumn
            )
        );

        $table = $this->createBaseNewTable();

        $columnDeptNo = $this->createBaseNewColumn();
        $columnDeptNo->setName(
            'deptNo'
        )->setType(
            'VARCHAR'
        )->setNullable(
            false
        )->setParameters(
            array(
                'length' => 4
            )
        );

        $columnDeptName = $this->createBaseNewColumn();
        $columnDeptName->setName(
            'deptName'
        )->setType(
            'VARCHAR'
        )->setNullable(
            false
        )->setParameters(
            array(
                'length' => 40
            )
        );

        $table2ReferencedIdColumn = $this->createBaseNewColumn();
        $table2ReferencedIdColumn->setName(
            'table2_id'
        )->setType(
            'INT'
        )->setNullable(
            false
        )->setParameters(
            array(
                'length' => 11
            )
        );

        $primaryKey = $this->createBaseNewPrimaryKey();
        $primaryKey->setColumns(
            array(
                $columnDeptNo
            )
        );

        $table->setName('departments')
            ->addColumns(
                array(
                    $columnDeptNo,
                    $columnDeptName,
                    $table2ReferencedIdColumn
                )
            );
        $table->setPrimaryKey($primaryKey);
        $index = $this->createBaseNewIndex();
        $index->setName(
            'unique_deptName'
        )->setKind(
            'UNIQUE'
        )->setColumns(
            array(
                $columnDeptName
            )
        );

        $table->setIndexes(
            array(
                $index
            )
        );

        $foreignKey = $this->createBaseNewForeignKey();
        $foreignKey->setColumns(
            array(
                $table2ReferencedIdColumn
            )
        )->setReferencedColumns(
            array(
                $table2IdColumn
            )
        )->setReferencedTable(
            $table2
        );

        $table->addForeignKey(
            $foreignKey
        );

        $expected = array(
            'name' => 'departments',
            'columns' => array(
                array(
                    'name' => 'deptNo',
                    'type' => 'VARCHAR',
                    'nullable' => false,
                    'parameters' => array(
                        'length' => 4,
                    ),
                    'autoIncrement' => false,
                ),
                array(
                    'name' => 'deptName',
                    'type' => 'VARCHAR',
                    'nullable' => false,
                    'parameters' => array(
                        'length' => 40,
                    ),
                    'autoIncrement' => false,
                ),
                array(
                    'name' => 'table2_id',
                    'type' => 'INT',
                    'nullable' => false,
                    'parameters' => array(
                        'length' => 11,
                    ),
                    'autoIncrement' => false,
                ),
            ),
            'primaryKey' => array(
                'columns' => array(
                    'deptNo',
                )
            ),
            'indexes' => array(
                array(
                    'name' => 'unique_deptName',
                    'kind' => 'UNIQUE',
                    'columns' => array(
                        'deptName',
                    ),
                ),
            ),
            'foreignKeys' => array(
                array(
                    'columns' => array(
                        'table2_id'
                    ),
                    'referencedTable' => 'table2',
                    'referencedColumns' => array(
                        'id'
                    ),
                    'onDelete' => null,
                    'onUpdate' => null,
                )
            )
        );

        $this->assertEquals(
            $expected,
            $table->toArray()
        );

        $table->setPrimaryKey(
            null
        );

        $expected['primaryKey'] = null;

        $this->assertEquals(
            $expected,
            $table->toArray()
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
     * @return Index
     */
    protected function createBaseNewIndex()
    {
        return $this->getMockForAbstractClass(
            Index::class
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

    /**
     * @return PrimaryKey
     */
    protected function createBaseNewPrimaryKey()
    {
        return $this->getMockForAbstractClass(
            PrimaryKey::class
        );
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
}
