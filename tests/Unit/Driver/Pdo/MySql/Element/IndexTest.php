<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\Index;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Tests\BaseTest;
use Calam\Dbal\Tests\Helper\CommonTestHelper;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class IndexTest extends BaseTest
{
    /**
     * @covers Calam\Dbal\SqlBased\Element\Index::__construct
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
     * @covers Calam\Dbal\SqlBased\Element\Index::getKind
     * @covers Calam\Dbal\SqlBased\Element\Index::setKind
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
     * @covers Calam\Dbal\SqlBased\Element\Index::getName
     * @covers Calam\Dbal\SqlBased\Element\Index::setName
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
     * @covers Calam\Dbal\SqlBased\Element\Index::getTable
     * @covers Calam\Dbal\SqlBased\Element\Index::setTable
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
     * @covers Calam\Dbal\SqlBased\Element\Index::getColumns
     * @covers Calam\Dbal\SqlBased\Element\Index::setColumns
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
     * @covers Calam\Dbal\SqlBased\Element\Index::toArray
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
        )->setType(
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
