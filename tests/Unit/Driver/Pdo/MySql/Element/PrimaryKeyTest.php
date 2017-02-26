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
use Calam\Dbal\Driver\Pdo\MySql\Element\PrimaryKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Tests\BaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class PrimaryKeyTest extends BaseTest
{
    /**
     * @covers PrimaryKey::__construct
     */
    public function testConstruct()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $primaryKey = new PrimaryKey($table, [ $column ]);

        $this->assertAttributeSame($table, 'table', $primaryKey);
        $this->assertAttributeSame([ $column ], 'columns', $primaryKey);
    }

    /**
     * @covers PrimaryKey::getTable
     */
    public function testGetTable()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $primaryKey = new PrimaryKey($table, [ $column ]);

        $this->assertSame($table, $primaryKey->getTable());
    }

    /**
     * @covers PrimaryKey::getColumns
     */
    public function testGetColumns()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $primaryKey = new PrimaryKey($table, [ $column ]);

        $this->assertSame([ $column ], $primaryKey->getColumns());
    }

    /**
     * @covers PrimaryKey::toArray
     */
    public function testToArray()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $primaryKey = new PrimaryKey($table, [ $column ]);

        $arr = $primaryKey->toArray();

        $this->assertEquals(
            [
                'table' => 'foo',
                'columns' => [
                    'id',
                ],
            ],
            $arr
        );
    }
}
