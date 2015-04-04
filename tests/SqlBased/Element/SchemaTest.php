<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Tests\SqlBased\Element;

use Arlekin\Core\Collection\ArrayCollection;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\View;
use Arlekin\Core\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class SchemaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema::__construct
     */
    public function testConstruct()
    {
        $schema = $this->createBaseNewSchema();

        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'tables',
            $schema
        );
        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'views',
            $schema
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema::getTables
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema::setTables
     */
    public function testGetAndSetTables()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewSchema(),
            'tables',
            array(
                $this->createBaseNewTable()
            )
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema::addTable
     */
    public function testAddTable()
    {
        $schema = $this->createBaseNewSchema();

        $schema->setTables(
            array(
                $this->createBaseNewTable()
            )
        );

        CommonTestHelper::testBasicAddForProperty(
            $this,
            $schema,
            'tables',
            $this->createBaseNewTable()
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema::addTables
     */
    public function testAddTables()
    {
        $schema = $this->createBaseNewSchema();

        $schema->setTables(
            array(
                $this->createBaseNewTable()
            )
        );

        CommonTestHelper::testBasicAddCollectionForProperty(
            $this,
            $schema,
            'tables',
            array(
                $this->createBaseNewTable()
            )
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema::getViews
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema::setViews
     */
    public function testGetAndSetViews()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewSchema(),
            'views',
            array(
                $this->createBaseNewView()
            )
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema::addView
     */
    public function testAddView()
    {
        $schema = $this->createBaseNewSchema();

        $schema->setViews(
            array(
                $this->createBaseNewView()
            )
        );

        CommonTestHelper::testBasicAddForProperty(
            $this,
            $schema,
            'views',
            $this->createBaseNewView()
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema::addViews
     */
    public function testAddViews()
    {
        $schema = $this->createBaseNewSchema();

        $schema->setViews(
            array(
                $this->createBaseNewView()
            )
        );

        CommonTestHelper::testBasicAddCollectionForProperty(
            $this,
            $schema,
            'views',
            array(
                $this->createBaseNewView()
            )
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema::toArray
     */
    public function testToArray()
    {
        $table = $this->createBaseNewTable();
        $view = $this->createBaseNewView();

        $schema = $this->createBaseNewSchema();

        $schema->setTables(
            array(
                $table
            )
        )->setViews(
            array(
                $view
            )
        );

        $this->assertSame(
            $schema->toArray(),
            array(
                'tables' => array(
                    array(
                        'name' => null,
                        'columns' => array(),
                        'primaryKey' => null,
                        'indexes' => array(),
                        'foreignKeys' => array(),
                    ),
                ),
                'views' => array(
                    array(
                        'name' => null,
                        'definition' => null,
                    ),
                ),
            )
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