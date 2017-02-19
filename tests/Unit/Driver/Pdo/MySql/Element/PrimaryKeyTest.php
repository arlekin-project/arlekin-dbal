<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\PrimaryKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Tests\BaseTest;
use Calam\Dbal\Tests\Helper\CommonTestHelper;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class PrimaryKeyTest extends BaseTest
{
    /**
     * @covers Calam\Dbal\SqlBased\Element\PrimaryKey::__construct
     */
    public function testConstruct()
    {
        $primaryKey = $this->createBaseNewPrimaryKey();

        $this->assertAttributeSame(null, 'table', $primaryKey);
        $this->assertAttributeSame([], 'columns', $primaryKey);
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\PrimaryKey::getTable
     * @covers Calam\Dbal\SqlBased\Element\PrimaryKey::setTable
     */
    public function testGetAndSetTable()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewPrimaryKey(),
            'table',
            $this->createBaseNewTable()
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\PrimaryKey::getColumns
     * @covers Calam\Dbal\SqlBased\Element\PrimaryKey::setColumns
     */
    public function testGetAndSetColumns()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewPrimaryKey(),
            'columns',
            [
                $this->createBaseNewColumn(),
            ]
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\PrimaryKey::toArray
     */
    public function testToArray()
    {
        $column = $this->createBaseNewColumn();

        $column->setName('deptNo');

        $primaryKey = $this->createBaseNewPrimaryKey();

        $primaryKey->addColumn($column);

        $table = $this->createBaseNewTable();

        $table->setName(
            'testTableName'
        )->setPrimaryKey(
            $primaryKey
        )->setColumns(
            [
                $column,
            ]
        );

        $arr = $primaryKey->toArray();

        $expected = [
            'columns' => [
                'deptNo',
            ],
            'table' => 'testTableName',
        ];

        $this->assertEquals($expected, $arr);
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
