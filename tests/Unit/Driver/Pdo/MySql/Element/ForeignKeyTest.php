<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnDeleteReferenceOptions;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnUpdateReferenceOptions;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Tests\BaseTest;
use Calam\Dbal\Tests\Helper\CommonTestHelper;

class ForeignKeyTest extends BaseTest
{
    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::__construct
     */
    public function testConstruct()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $this->assertAttributeSame(null, 'table', $foreignKey);
        $this->assertAttributeSame([], 'columns', $foreignKey);
        $this->assertAttributeSame(null, 'referencedTable', $foreignKey);
        $this->assertAttributeSame([], 'referencedColumns', $foreignKey);

        $this->assertAttributeSame(
            ForeignKeyOnDeleteReferenceOptions::ON_DELETE_RESTRICT,
            'onDelete',
            $foreignKey
        );

        $this->assertAttributeSame(
            ForeignKeyOnUpdateReferenceOptions::ON_UPDATE_RESTRICT,
            'onUpdate',
            $foreignKey
        );
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getTable
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setTable
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
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getColumns
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setColumns
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
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getReferencedTable
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setReferencedTable
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
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getReferencedColumns
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setReferencedColumns
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
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getOnDelete
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setOnDelete
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
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getOnUpdate
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setOnUpdate
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
     * @covers Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey::toArray
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
            'onDelete' => ForeignKeyOnDeleteReferenceOptions::ON_DELETE_RESTRICT,
            'onUpdate' => ForeignKeyOnUpdateReferenceOptions::ON_UPDATE_RESTRICT,
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
