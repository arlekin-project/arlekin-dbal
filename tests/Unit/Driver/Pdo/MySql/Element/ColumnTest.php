<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Unit\Driver\Pdo\MySql\Element;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Tests\BaseTest;
use Arlekin\Dbal\Tests\Helper\CommonTestHelper;
use Exception;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ColumnTest extends BaseTest
{
    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Column::__construct
     */
    public function testConstruct()
    {
        $column = $this->createBaseNewColumn();

        $this->assertAttributeSame(false, 'autoIncrement', $column);
        $this->assertAttributeSame([], 'parameters', $column);
    }

    /**
     * @coversNothing
     */
    public function testDefaultValues()
    {
        $column = $this->createBaseNewColumn();

        $this->assertEquals(
            null,
            $column->getName()
        );
        $this->assertEquals(
            null,
            $column->getType()
        );
        $this->assertEquals(
            null,
            $column->isNullable()
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Column::getName
     * @covers Arlekin\Dbal\SqlBased\Element\Column::setName
     */
    public function testGetAndSetName()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewColumn(),
            'name',
            uniqid('name_', true)
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Column::getType
     * @covers Arlekin\Dbal\SqlBased\Element\Column::setType
     */
    public function testGetAndSetType()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewColumn(),
            'type',
            'TEST'
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Column::isNullable
     * @covers Arlekin\Dbal\SqlBased\Element\Column::setNullable
     */
    public function testIsAndSetNullable()
    {
        CommonTestHelper::testBasicIsAndSetForProperty(
            $this,
            $this->createBaseNewColumn(),
            'nullable',
            true
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Column::isAutoIncrement
     * @covers Arlekin\Dbal\SqlBased\Element\Column::setAutoIncrement
     */
    public function testIsAndSetAutoIncrement()
    {
        CommonTestHelper::testBasicIsAndSetForProperty(
            $this,
            $this->createBaseNewColumn(),
            'autoIncrement',
            true
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Column::getParameters
     * @covers Arlekin\Dbal\SqlBased\Element\Column::setParameters
     */
    public function testGetAndSetParameters()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewColumn(),
            'parameters',
            [
                'test' => 42,
            ]
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Column::getTable
     * @covers Arlekin\Dbal\SqlBased\Element\Column::setTable
     */
    public function testGetAndSetTable()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewColumn(),
            'table',
            $this->createBaseNewTable()
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Column::toArray
     */
    public function testToArrayNoTableExceptionIfNoTable()
    {
        CommonTestHelper::assertExceptionThrown(
            function () {
                $column = $this->createBaseTestColumn();
                $column->toArray();
            },
            Exception::class,
            'Missing table for column "testName".'
        );
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Column::toArray
     */
    public function testToArray()
    {
        $column = $this->createBaseTestColumn();

        $table = $this->createBaseNewTable();

        $table->setName(
            'testTableName'
        )->addColumn(
            $column
        );

        $column->setType('VARCHAR');

        $arr = $column->toArray();

        $expected = [
            'name' => 'testName',
            'type' => 'VARCHAR',
            'nullable' => false,
            'parameters' => [
                'testParameterKey' => 'testParameterName',
            ],
            'table' => 'testTableName',
            'autoIncrement' => false,
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
    protected function createBaseTestColumn()
    {
        $columnName = 'testName';
        $nullable = false;

        $column = $this->createBaseNewColumn();

        $column->setName(
            $columnName
        )->setNullable(
            $nullable
        )->setParameters(
            [
                'testParameterKey' => 'testParameterName',
            ]
        );

        return $column;
    }
}