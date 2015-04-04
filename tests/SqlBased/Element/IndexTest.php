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
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table;
use Arlecchino\Core\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class IndexTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::__construct
     */
    public function testConstruct()
    {
        $index = $this->createBaseNewIndex();

        $this->assertAttributeSame(
            null,
            'kind',
            $index
        );
        $this->assertAttributeSame(
            null,
            'name',
            $index
        );
        $this->assertAttributeSame(
            null,
            'table',
            $index
        );
        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'columns',
            $index
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::getKind
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::setKind
     */
    public function testGetAndSetKind()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $index = $this->createBaseNewIndex(),
            'kind',
            'BTREE'
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::getName
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::setName
     */
    public function testGetAndSetName()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $index = $this->createBaseNewIndex(),
            'name',
            uniqid(
                'test_name_',
                true
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::getTable
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::setTable
     */
    public function testGetAndSetTable()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewIndex(),
            'table',
            $this->createBaseNewTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::getColumns
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::setColumns
     */
    public function testGetAndSetColumns()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewIndex(),
            'columns',
            array(
                $this->createBaseNewColumn()
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::addColumn
     */
    public function testAddColumn()
    {
        CommonTestHelper::testBasicAddForProperty(
            $this,
            $this->createBaseNewIndex(),
            'columns',
            $this->createBaseNewColumn()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::addColumns
     */
    public function testAddColumns()
    {
        CommonTestHelper::testBasicAddCollectionForProperty(
            $this,
            $this->createBaseNewIndex(),
            'columns',
            array(
                $this->createBaseNewColumn()
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index::toArray
     */
    public function testToArrayNoTable()
    {
        $index = $this->createBaseTestIndexWithTable();
        $arr = $index->toArray();
        $expected = array(
            'name' => 'unique_deptName',
            'kind' => 'UNIQUE',
            'columns' => array(
                'deptName'
            ),
            'table' => 'testTableName'
        );

        $this->assertEquals(
            $expected,
            $arr
        );
    }

    /**
     * @return Index
     */
    protected function createBaseTestIndex()
    {
        $index = $this->createBaseNewIndex();
        $index->setName(
            'unique_deptName'
        )->setKind(
            'UNIQUE'
        );

        $column = $this->createBaseNewColumn();
        $column->setName(
            'deptName'
        );

        $index->setColumns(
            array(
                $column
            )
        );

        return array(
            'index' => $index,
            'column' => $column
        );
    }

    /**
     * @return Index
     */
    protected function createBaseTestIndexWithTable()
    {
        $result = $this->createBaseTestIndex();
        $index = $result['index'];
        $column = $result['column'];
        $table = $this->createBaseNewTable();
        $table->setName('testTableName');
        $column->setTable($table);
        $index->setTable($table);

        return $index;
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
     * @return Index
     */
    protected function createBaseNewIndex()
    {
        return $this->getMockForAbstractClass(
            Index::class
        );
    }
}
