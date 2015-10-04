<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Tests\SqlBased\Element;

use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Column;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\ForeignKey;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table;
use Arlekin\Core\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class TableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::__construct
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
        $this->assertAttributeSame(
            [],
            'columns',
            $table
        );
        $this->assertAttributeSame(
            [],
            'foreignKeys',
            $table
        );
        $this->assertAttributeSame(
            [],
            'indexes',
            $table
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::getName
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::setName
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
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::getPrimaryKey
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::setPrimaryKey
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
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::getColumns
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::setColumns
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
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::getForeignKeys
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::setForeignKeys
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
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::getIndexes
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::setIndexes
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
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table::toArray
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

        $table->setName(
            'departments'
        )->setColumns(
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
