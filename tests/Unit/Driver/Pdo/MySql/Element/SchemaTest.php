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
     * @covers Calam\Dbal\SqlBased\Element\Schema::__construct
     */
    public function testConstruct()
    {
        $schema = $this->createBaseNewSchema();

        $this->assertAttributeSame([], 'tables', $schema);
        $this->assertAttributeSame([], 'views', $schema);
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Schema::getTables
     * @covers Calam\Dbal\SqlBased\Element\Schema::setTables
     */
    public function testGetAndSetTables()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewSchema(),
            'tables',
            [
                $this->createBaseNewTable(),
            ]
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Schema::getViews
     * @covers Calam\Dbal\SqlBased\Element\Schema::setViews
     */
    public function testGetAndSetViews()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewSchema(),
            'views',
            [
                $this->createBaseNewView(),
            ]
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Schema::toArray
     */
    public function testToArray()
    {
        $table = $this->createBaseNewTable();
        $view = $this->createBaseNewView();

        $schema = $this->createBaseNewSchema();

        $schema->setTables(
            [
                $table,
            ]
        )->setViews(
            [
                $view,
            ]
        );

        $this->assertSame(
            $schema->toArray(),
            [
                'tables' => [
                    [
                        'name' => null,
                        'columns' => [],
                        'primaryKey' => null,
                        'indexes' => [],
                        'foreignKeys' => [],
                    ],
                ],
                'views' => [
                    [
                        'name' => null,
                        'definition' => null,
                    ],
                ],
            ]
        );
    }
    
    /**
     * @covers Calam\Dbal\SqlBased\Element\Schema::getTableWithName
     * @covers Calam\Dbal\SqlBased\Element\Schema::doGetWithName
     */
    public function testGetTableWithName()
    {
        $schema = new Schema();

        $table = new Table();

        $table->setName('testTable');

        $schema->addTable($table);

        $retrievedTable = $schema->getTableWithName('testTable');

        $this->assertSame($table, $retrievedTable);
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Schema::getTableWithName
     * @covers Calam\Dbal\SqlBased\Element\Schema::doGetWithName
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
     * @covers Calam\Dbal\SqlBased\Element\Schema::hasTableWithName
     * @covers Calam\Dbal\SqlBased\Element\Schema::doHasWithName
     */
    public function testHasTableWithName()
    {
        $schema = new Schema();

        $table = new Table();

        $table->setName('testTable');

        $this->assertFalse(
            $schema->hasTableWithName('testTable')
        );

        $schema->addTable($table);

        $this->assertTrue(
            $schema->hasTableWithName('testTable')
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Schema::getViewWithName
     * @covers Calam\Dbal\SqlBased\Element\Schema::doGetWithName
     */
    public function testGetViewWithName()
    {
        $schema = new Schema();

        $view = new View();

        $view->setName('testView');

        $schema->addView($view);

        $this->assertSame(
            $view,
            $schema->getViewWithName('testView')
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Schema::getViewWithName
     * @covers Calam\Dbal\SqlBased\Element\Schema::doGetWithName
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
     * @covers Calam\Dbal\SqlBased\Element\Schema::hasViewWithName
     * @covers Calam\Dbal\SqlBased\Element\Schema::doHasWithName
     */
    public function testHasViewWithName()
    {
        $schema = new Schema();

        $view = new View();

        $view->setName('testView');

        $this->assertFalse(
            $schema->hasViewWithName('testView')
        );

        $schema->addView($view);

        $this->assertTrue(
            $schema->hasViewWithName('testView')
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
     * @return View
     */
    protected function createBaseNewView()
    {
        return $this->getMockForAbstractClass(
            View::class
        );
    }

    /**
     * @return Schema
     */
    protected function createBaseNewSchema()
    {
        return $this->getMockForAbstractClass(
            Schema::class
        );
    }
}