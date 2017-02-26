<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\Schema;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Driver\Pdo\MySql\Element\View;
use Calam\Dbal\Tests\BaseTest;
use Calam\Dbal\Tests\Helper\CommonTestHelper;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class SchemaTest extends BaseTest
{
    /**
     * @covers Schema::__construct
     */
    public function testConstruct()
    {
        $table = new Table('foo');
        $view = new View('bar', 'SELECT 1');

        $schema = new Schema([ $table ], [ $view ]);

        $this->assertAttributeSame([ $table ], 'tables', $schema);
        $this->assertAttributeSame([ $view ], 'views', $schema);
    }

    /**
     * @covers Schema::getTables
     */
    public function testGetTables()
    {
        $table = new Table('foo');

        $schema = new Schema([ $table ], []);

        $this->assertSame([ $table ], $schema->getTables());
    }

    /**
     * @covers Schema::getViews
     */
    public function testGetViews()
    {
        $view = new View('bar', 'SELECT 1');

        $schema = new Schema([], [ $view ]);

        $this->assertSame([ $view ], $schema->getViews());
    }
    
    /**
     * @covers Schema::getTableWithName
     * @covers Schema::doGetWithName
     */
    public function testGetTableWithName()
    {
        $table = new Table('testTable');

        $schema = new Schema([ $table ]);

        $retrievedTable = $schema->getTableWithName('testTable');

        $this->assertSame($table, $retrievedTable);
    }

    /**
     * @covers Schema::getTableWithName
     * @covers Schema::doGetWithName
     */
    public function testGetTableWithNameExceptionThrownIfNoTableWithName()
    {
        $schema = new Schema();

        CommonTestHelper::assertExceptionThrown(
            function () use ($schema) {
                $schema->getTableWithName('testTable');
            },
            \Exception::class,
            'Found no table with name "testTable" in schema.'
        );
    }

    /**
     * @covers Schema::hasTableWithName
     * @covers Schema::doHasWithName
     */
    public function testHasTableWithName()
    {
        $table = new Table('testTable');

        $schema = new Schema();

        $this->assertFalse(
            $schema->hasTableWithName('testTable')
        );

        $schema = new Schema([ $table ]);

        $this->assertTrue(
            $schema->hasTableWithName('testTable')
        );
    }

    /**
     * @covers Schema::getViewWithName
     * @covers Schema::doGetWithName
     */
    public function testGetViewWithName()
    {
        $view = new View('testView', 'SELECT 1');

        $schema = new Schema([], [ $view ]);

        $this->assertSame(
            $view,
            $schema->getViewWithName('testView')
        );
    }

    /**
     * @covers Schema::getViewWithName
     * @covers Schema::doGetWithName
     */
    public function testGetViewWithNameExceptionThrownIfNoViewWithName()
    {
        $schema = new Schema();

        CommonTestHelper::assertExceptionThrown(
            function () use ($schema) {
                $schema->getViewWithName('testView');
            },
            \Exception::class,
            'Found no view with name "testView" in schema.'
        );
    }

    /**
     * @covers Schema::hasViewWithName
     * @covers Schema::doHasWithName
     */
    public function testHasViewWithName()
    {
        $view = new View('testView', 'SELECT 1');

        $schema = new Schema();

        $this->assertFalse(
            $schema->hasViewWithName('testView')
        );

        $schema = new Schema([], [ $view ]);

        $this->assertTrue(
            $schema->hasViewWithName('testView')
        );
    }
}
