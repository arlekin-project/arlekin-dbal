<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\SqlBased\Element;

use Arlekin\Dbal\SqlBased\Element\Schema;
use Arlekin\Dbal\SqlBased\Element\Table;
use Arlekin\Dbal\SqlBased\Element\View;
use Arlekin\Dbal\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class SchemaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Schema::__construct
     */
    public function testConstruct()
    {
        $schema = $this->createBaseNewSchema();

        $this->assertAttributeSame([], 'tables', $schema);
        $this->assertAttributeSame([], 'views', $schema);
    }

    /**
     * @covers Arlekin\Dbal\SqlBased\Element\Schema::getTables
     * @covers Arlekin\Dbal\SqlBased\Element\Schema::setTables
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
     * @covers Arlekin\Dbal\SqlBased\Element\Schema::getViews
     * @covers Arlekin\Dbal\SqlBased\Element\Schema::setViews
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
     * @covers Arlekin\Dbal\SqlBased\Element\Schema::toArray
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