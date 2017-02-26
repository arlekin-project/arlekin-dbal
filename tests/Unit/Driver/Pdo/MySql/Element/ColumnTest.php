<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\ColumnDataTypes;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Tests\BaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class ColumnTest extends BaseTest
{
    /**
     * @covers Column::__construct
     */
    public function testConstruct()
    {
        $table = new Table('foo');

        $column = new Column($table, 'id', ColumnDataTypes::TYPE_INT, false, true, [ 'foo' => 'bar', ]);

        $this->assertAttributeSame($table, 'table', $column);
        $this->assertAttributeSame('id', 'name', $column);
        $this->assertAttributeSame(ColumnDataTypes::TYPE_INT, 'dataType', $column);
        $this->assertAttributeSame(false, 'nullable', $column);
        $this->assertAttributeSame(true, 'autoIncrementable', $column);
        $this->assertAttributeSame([ 'foo' => 'bar', ], 'parameters', $column);
    }

    /**
     * @covers Column::__construct
     */
    public function testDefaultValues()
    {
        $table = new Table('foo');

        $column = new Column($table, 'id', ColumnDataTypes::TYPE_INT, false);

        $this->assertAttributeSame('id', 'name', $column);
        $this->assertAttributeSame(ColumnDataTypes::TYPE_INT, 'dataType', $column);
        $this->assertAttributeSame(false, 'nullable', $column);
        $this->assertAttributeSame(false, 'autoIncrementable', $column);
        $this->assertAttributeSame([], 'parameters', $column);
    }

    /**
     * @covers Column::getName
     */
    public function testGetName()
    {
        $table = new Table('foo');

        $column = new Column($table, 'id', ColumnDataTypes::TYPE_INT, false);

        $this->assertSame(
            'id',
            $column->getName()
        );
    }

    /**
     * @covers Column::getDataType
     */
    public function testGetDataType()
    {
        $table = new Table('foo');

        $column = new Column($table, 'id', ColumnDataTypes::TYPE_INT, false);

        $this->assertSame(
            ColumnDataTypes::TYPE_INT,
            $column->getDataType()
        );
    }

    /**
     * @covers Column::isNullable
     */
    public function testIsNullable()
    {
        $table = new Table('foo');

        $column = new Column($table, 'id', ColumnDataTypes::TYPE_INT, false);

        $this->assertFalse($column->isNullable());
    }

    /**
     * @covers Column::isAutoIncrementable
     */
    public function testIsAutoIncrementable()
    {
        $table = new Table('foo');

        $column = new Column($table, 'id', ColumnDataTypes::TYPE_INT, false, true);

        $this->assertTrue($column->isAutoIncrementable());
    }

    /**
     * @covers Column::getParameters
     */
    public function testGetParameters()
    {
        $table = new Table('foo');

        $column = new Column($table, 'id', ColumnDataTypes::TYPE_INT, false, true, [ 'foo' => 'bar' ]);

        $this->assertSame([ 'foo' => 'bar' ], $column->getParameters());
    }
}
