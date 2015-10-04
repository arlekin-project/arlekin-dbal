<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Tests\SqlBased\Element;

use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Column;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table;
use Arlekin\Core\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class IndexTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index::__construct
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
        $this->assertAttributeSame(
            [],
            'columns',
            $index
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index::getKind
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index::setKind
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
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index::getName
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index::setName
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
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index::getTable
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index::setTable
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
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index::getColumns
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index::setColumns
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
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Index::toArray
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
