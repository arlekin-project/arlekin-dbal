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

class ForeignKeyTest extends BaseTest
{
    /**
     * @covers ForeignKey::__construct
     */
    public function testConstruct()
    {
        $table = new Table('foo');

        $referenceTable = new Table('bar');

        $column = new Column($table, 'id');

        $referencedColumn = new Column($referenceTable, 'id');

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
        $table = new Table('foo');

        $referenceTable = new Table('bar');

        $column = new Column($table, 'id');

        $referencedColumn = new Column($referenceTable, 'id');

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
        $table = new Table('foo');

        $referenceTable = new Table('bar');

        $column = new Column($table, 'id');

        $referencedColumn = new Column($referenceTable, 'id');

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
        $table = new Table('foo');

        $referenceTable = new Table('bar');

        $column = new Column($table, 'id');

        $referencedColumn = new Column($referenceTable, 'id');

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
        $table = new Table('foo');

        $referenceTable = new Table('bar');

        $column = new Column($table, 'id');

        $referencedColumn = new Column($referenceTable, 'id');

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
        $table = new Table('foo');

        $referenceTable = new Table('bar');

        $column = new Column($table, 'id');

        $referencedColumn = new Column($referenceTable, 'id');

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
        $table = new Table('foo');

        $referenceTable = new Table('bar');

        $column = new Column($table, 'id');

        $referencedColumn = new Column($referenceTable, 'id');

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
        $table = new Table('foo');

        $referenceTable = new Table('bar');

        $column = new Column($table, 'id');

        $referencedColumn = new Column($referenceTable, 'id');

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
}
