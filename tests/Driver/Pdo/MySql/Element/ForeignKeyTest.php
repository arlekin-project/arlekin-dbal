<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Driver\Pdo\MySql\Element;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnDeleteConstraint;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnUpdateConstraint;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Tests\Driver\Pdo\MySql\AbstractBasePdoMySqlTest;
use Arlekin\Dbal\Tests\Helper\CommonTestHelper;

class ForeignKeyTest extends AbstractBasePdoMySqlTest
{
    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::__construct
     */
    public function testConstruct()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $this->assertAttributeSame(null, 'table', $foreignKey);
        $this->assertAttributeSame([], 'columns', $foreignKey);
        $this->assertAttributeSame(null, 'referencedTable', $foreignKey);
        $this->assertAttributeSame([], 'referencedColumns', $foreignKey);

        $this->assertAttributeSame(
            ForeignKeyOnDeleteConstraint::ON_DELETE_RESTRICT,
            'onDelete',
            $foreignKey
        );

        $this->assertAttributeSame(
            ForeignKeyOnUpdateConstraint::ON_UPDATE_RESTRICT,
            'onUpdate',
            $foreignKey
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getTable
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setTable
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
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getColumns
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setColumns
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
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getReferencedTable
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setReferencedTable
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
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getReferencedColumns
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setReferencedColumns
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
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getOnDelete
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setOnDelete
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
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::getOnUpdate
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::setOnUpdate
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
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::toArray
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
            'onDelete' => ForeignKeyOnDeleteConstraint::ON_DELETE_RESTRICT,
            'onUpdate' => ForeignKeyOnUpdateConstraint::ON_UPDATE_RESTRICT,
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
