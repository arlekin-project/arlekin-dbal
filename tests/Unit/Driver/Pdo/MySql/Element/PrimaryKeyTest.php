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
        $table = new Table('foo');

        $column = new Column($table, 'id');

        $primaryKey = new PrimaryKey($table, [ $column ]);

        $this->assertAttributeSame($table, 'table', $primaryKey);
        $this->assertAttributeSame([ $column ], 'columns', $primaryKey);
    }

    /**
     * @covers PrimaryKey::getTable
     */
    public function testGetTable()
    {
        $table = new Table('foo');

        $column = new Column($table, 'id');

        $primaryKey = new PrimaryKey($table, [ $column ]);

        $this->assertSame($table, $primaryKey->getTable());
    }

    /**
     * @covers PrimaryKey::getColumns
     */
    public function testGetColumns()
    {
        $table = new Table('foo');

        $column = new Column($table, 'id');

        $primaryKey = new PrimaryKey($table, [ $column ]);

        $this->assertSame([ $column ], $primaryKey->getColumns());
    }
}
