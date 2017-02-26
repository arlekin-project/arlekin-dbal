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
use Calam\Dbal\Driver\Pdo\MySql\Element\Exception\UnknownIndexClassException;
use Calam\Dbal\Driver\Pdo\MySql\Element\Exception\UnknownIndexTypeException;
use Calam\Dbal\Driver\Pdo\MySql\Element\Index;
use Calam\Dbal\Driver\Pdo\MySql\Element\IndexClasses;
use Calam\Dbal\Driver\Pdo\MySql\Element\IndexTypes;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Tests\BaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class IndexTest extends BaseTest
{
    /**
     * @covers Index::__construct
     */
    public function testConstruct()
    {
        $column = new Column('uniqueValue', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $index = new Index(
            $table,
            'foo',
            [
                $column
            ],
            IndexClasses::UNIQUE,
            IndexTypes::BTREE
        );

        $this->assertAttributeSame($table, 'table', $index);
        $this->assertAttributeSame('foo', 'name', $index);
        $this->assertAttributeSame([ $column ], 'columns', $index);
        $this->assertAttributeSame(IndexClasses::UNIQUE, 'class', $index);
        $this->assertAttributeSame(IndexTypes::BTREE, 'type', $index);
    }

    /**
     * @covers Index::__construct
     */
    public function testConstructUnknownIndexClassException()
    {
        $this->expectException(UnknownIndexClassException::class);
        $this->expectExceptionMessageRegExp(
            '/^'.preg_quote('Unknown index class "foo". Known index classes are ["').'.*'.preg_quote('"].').'$/'
        );

        $column = new Column('uniqueValue', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        new Index(
            $table,
            'foo',
            [
                $column
            ],
            'foo',
            IndexTypes::BTREE
        );
    }

    /**
     * @covers Index::__construct
     */
    public function testConstructUnknownIndexTypeException()
    {
        $this->expectException(UnknownIndexTypeException::class);
        $this->expectExceptionMessageRegExp(
            '/^'.preg_quote('Unknown index type "foo". Known index types are ["').'.*'.preg_quote('"].').'$/'
        );

        $column = new Column('uniqueValue', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        new Index(
            $table,
            'foo',
            [
                $column
            ],
            IndexClasses::UNIQUE,
            'foo'
        );
    }

    /**
     * @covers Index::getTable
     */
    public function testGetTable()
    {
        $column = new Column('uniqueValue', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $index = new Index(
            $table,
            'foo',
            [
                $column
            ],
            IndexClasses::UNIQUE,
            IndexTypes::BTREE
        );

        $this->assertSame($table, $index->getTable());
    }

    /**
     * @covers Index::getName
     */
    public function testGetName()
    {
        $column = new Column('uniqueValue', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $index = new Index(
            $table,
            'foo',
            [
                $column
            ],
            IndexClasses::UNIQUE,
            IndexTypes::BTREE
        );

        $this->assertSame('foo', $index->getName());
    }

    /**
     * @covers Index::getColumns
     */
    public function testGetColumns()
    {
        $column = new Column('uniqueValue', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $index = new Index(
            $table,
            'foo',
            [
                $column
            ],
            IndexClasses::UNIQUE,
            IndexTypes::BTREE
        );

        $this->assertSame([$column ], $index->getColumns());
    }

    /**
     * @covers Index::toArray
     */
    public function testToArray()
    {
        $column = new Column('uniqueValue', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $index = new Index(
            $table,
            'fooIndex',
            [
                $column
            ],
            IndexClasses::UNIQUE,
            IndexTypes::BTREE
        );

        $arr = $index->toArray();

        $expected = [
            'name' => 'fooIndex',
            'class' => IndexClasses::UNIQUE,
            'type' => IndexTypes::BTREE,
            'columns' => [
                'uniqueValue',
            ],
            'table' => 'foo',
        ];

        $this->assertEquals($expected, $arr);
    }
}
