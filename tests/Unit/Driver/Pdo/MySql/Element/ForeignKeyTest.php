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
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnDeleteReferenceOptions;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnUpdateReferenceOptions;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Tests\BaseTest;

class ForeignKeyTest extends BaseTest
{
    /**
     * @covers ForeignKey::__construct
     */
    public function testConstruct()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $referencedColumn = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $referenceTable = new Table('bar', [ $referencedColumn ]);

        $foreignKey = new ForeignKey(
            $table,
            [ $column ],
            $referenceTable,
            [ $referencedColumn ],
            ForeignKeyOnDeleteReferenceOptions::ON_DELETE_CASCADE,
            ForeignKeyOnUpdateReferenceOptions::ON_UPDATE_SET_DEFAULT
        );

        $this->assertAttributeSame($table, 'table', $foreignKey);
        $this->assertAttributeSame([ $column ], 'columns', $foreignKey);
        $this->assertAttributeSame($referenceTable, 'referencedTable', $foreignKey);
        $this->assertAttributeSame([ $referencedColumn ], 'referencedColumns', $foreignKey);

        $this->assertAttributeSame(
            ForeignKeyOnDeleteReferenceOptions::ON_DELETE_CASCADE,
            'onDelete',
            $foreignKey
        );

        $this->assertAttributeSame(
            ForeignKeyOnUpdateReferenceOptions::ON_UPDATE_SET_DEFAULT,
            'onUpdate',
            $foreignKey
        );
    }

    /**
     * @covers ForeignKey::__construct
     */
    public function testConstructDefaultValue()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $referencedColumn = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $referenceTable = new Table('bar', [ $referencedColumn ]);

        $foreignKey = new ForeignKey($table, [ $column ], $referenceTable, [ $referencedColumn ]);

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
     * @covers ForeignKey::getTable
     */
    public function testGetTable()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $referencedColumn = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $referenceTable = new Table('bar', [ $referencedColumn ]);

        $foreignKey = new ForeignKey(
            $table,
            [ $column ],
            $referenceTable,
            [ $referencedColumn ]
        );

        $this->assertSame($table, $foreignKey->getTable());
    }

    /**
     * @covers ForeignKey::getColumns
     */
    public function testGetColumns()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $referencedColumn = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $referenceTable = new Table('bar', [ $referencedColumn ]);

        $foreignKey = new ForeignKey(
            $table,
            [ $column ],
            $referenceTable,
            [ $referencedColumn ]
        );

        $this->assertSame([ $column ], $foreignKey->getColumns());
    }

    /**
     * @covers ForeignKey::getReferencedTable
     */
    public function testGetReferencedTable()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $referencedColumn = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $referenceTable = new Table('bar', [ $referencedColumn ]);

        $foreignKey = new ForeignKey(
            $table,
            [ $column ],
            $referenceTable,
            [ $referencedColumn ]
        );

        $this->assertSame($referenceTable, $foreignKey->getReferencedTable());
    }

    /**
     * @covers ForeignKey::getReferencedColumns
     */
    public function testGetReferencedColumns()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $referencedColumn = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $referenceTable = new Table('bar', [ $referencedColumn ]);

        $foreignKey = new ForeignKey(
            $table,
            [ $column ],
            $referenceTable,
            [ $referencedColumn ]
        );

        $this->assertSame([ $referencedColumn ], $foreignKey->getReferencedColumns());
    }

    /**
     * @covers ForeignKey::getOnDelete
     */
    public function testGetOnDelete()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $referencedColumn = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $referenceTable = new Table('bar', [ $referencedColumn ]);

        $foreignKey = new ForeignKey(
            $table,
            [ $column ],
            $referenceTable,
            [ $referencedColumn ],
            ForeignKeyOnDeleteReferenceOptions::ON_DELETE_CASCADE
        );

        $this->assertSame(ForeignKeyOnDeleteReferenceOptions::ON_DELETE_CASCADE, $foreignKey->getOnDelete());
    }

    /**
     * @covers ForeignKey::getOnUpdate
     */
    public function testGetOnUpdate()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $referencedColumn = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $referenceTable = new Table('bar', [ $referencedColumn ]);

        $foreignKey = new ForeignKey(
            $table,
            [ $column ],
            $referenceTable,
            [ $referencedColumn ],
            ForeignKeyOnDeleteReferenceOptions::ON_DELETE_CASCADE,
            ForeignKeyOnUpdateReferenceOptions::ON_UPDATE_SET_DEFAULT
        );

        $this->assertSame(ForeignKeyOnDeleteReferenceOptions::ON_DELETE_SET_DEFAULT, $foreignKey->getOnUpdate());
    }

    /**
     * @covers ForeignKey::toArray
     */
    public function testToArray()
    {
        $column = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $referencedColumn = new Column('id', ColumnDataTypes::TYPE_INT, false);

        $table = new Table('foo', [ $column ]);

        $referenceTable = new Table('bar', [ $referencedColumn ]);

        $foreignKey = new ForeignKey(
            $table,
            [ $column ],
            $referenceTable,
            [ $referencedColumn ],
            ForeignKeyOnDeleteReferenceOptions::ON_DELETE_CASCADE,
            ForeignKeyOnUpdateReferenceOptions::ON_UPDATE_SET_DEFAULT
        );

        $arr = $foreignKey->toArray();

        $expected = [
            'table' => 'foo',
            'columns' => [
                'id',
            ],
            'referencedTable' => 'bar',
            'referencedColumns' => [
                'id',
            ],
            'onDelete' => ForeignKeyOnDeleteReferenceOptions::ON_DELETE_CASCADE,
            'onUpdate' => ForeignKeyOnUpdateReferenceOptions::ON_UPDATE_SET_DEFAULT,
        ];

        $this->assertEquals($expected, $arr);
    }
}
