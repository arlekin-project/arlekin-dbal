<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\SqlBased\Element;

use Arlekin\Dbal\SqlBased\Element\Column;
use Arlekin\Dbal\SqlBased\Element\Index;
use Arlekin\Dbal\SqlBased\Element\Table;
use Arlekin\Dbal\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class IndexTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Index::__construct
     */
    public function testConstruct()
    {
        $index = $this->createBaseNewIndex();

        $this->assertAttributeSame(null, 'kind', $index);
        $this->assertAttributeSame(null, 'name', $index);
        $this->assertAttributeSame(null, 'table', $index);
        $this->assertAttributeSame([], 'columns', $index);
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Index::getKind
     * @covers Arlekin\Dbal\SqlBased\Element\Index::setKind
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
     * @covers Arlekin\Dbal\SqlBased\Element\Index::getName
     * @covers Arlekin\Dbal\SqlBased\Element\Index::setName
     */
    public function testGetAndSetName()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $index = $this->createBaseNewIndex(),
            'name',
            uniqid('test_name_', true)
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Index::getTable
     * @covers Arlekin\Dbal\SqlBased\Element\Index::setTable
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
     * @covers Arlekin\Dbal\SqlBased\Element\Index::getColumns
     * @covers Arlekin\Dbal\SqlBased\Element\Index::setColumns
     */
    public function testGetAndSetColumns()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewIndex(),
            'columns',
            [
                $this->createBaseNewColumn(),
            ]
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Index::toArray
     */
    public function testToArrayNoTable()
    {
        $index = $this->createBaseTestIndexWithTable();

        $arr = $index->toArray();

        $expected = [
            'name' => 'unique_deptName',
            'kind' => 'UNIQUE',
            'columns' => [
                'deptName',
            ],
            'table' => 'testTableName',
        ];

        $this->assertEquals($expected, $arr);
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

        $column->setName('deptName');

        $index->setColumns(
            [
                $column,
            ]
        );

        return [
            'index' => $index,
            'column' => $column,
        ];
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
