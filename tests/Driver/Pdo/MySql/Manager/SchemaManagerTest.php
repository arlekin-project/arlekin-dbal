<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Driver\Pdo\MySql;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\View;
use Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager;
use Arlekin\Dbal\Tests\Helper\CommonTestHelper;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class SchemaManagerTest extends AbstractBasePdoMySqlTest
{
    /**
     * @var SchemaManager
     */
    protected $schemaManager;

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::getTableWithName
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::doGetWithName
     */
    public function testGetTableWithName()
    {
        $schema = new Schema();

        $table = new Table();

        $table->setName('testTable');

        $schema->addTable($table);

        $retrievedTable = $this->schemaManager->getTableWithName($schema, 'testTable');

        $this->assertSame($table, $retrievedTable);
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::getTableWithName
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::doGetWithName
     */
    public function testGetTableWithNameExceptionThrownIfNoTableWithName()
    {
        $schema = new Schema();

        CommonTestHelper::assertExceptionThrown(
            function () use ($schema) {
                $this->schemaManager->getTableWithName($schema, 'testTable');
            },
            \Exception::class,
            'Found no table with name "testTable" in schema.'
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::hasTableWithName
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::doHasWithName
     */
    public function testHasTableWithName()
    {
        $schema = new Schema();

        $table = new Table();

        $table->setName('testTable');

        $this->assertFalse(
            $this->schemaManager->hasTableWithName($schema, 'testTable')
        );

        $schema->addTable($table);

        $this->assertTrue(
            $this->schemaManager->hasTableWithName($schema, 'testTable')
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::getViewWithName
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::doGetWithName
     */
    public function testGetViewWithName()
    {
        $schema = new Schema();

        $view = new View();

        $view->setName('testView');

        $schema->addView($view);

        $this->assertSame(
            $view,
            $this->schemaManager->getViewWithName($schema, 'testView')
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::getViewWithName
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::doGetWithName
     */
    public function testGetViewWithNameExceptionThrownIfNoViewWithName()
    {
        $schema = new Schema();

        CommonTestHelper::assertExceptionThrown(
            function () use ($schema) {
                $this->schemaManager->getViewWithName($schema, 'testView');
            },
            \Exception::class,
            'Found no view with name "testView" in schema.'
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::hasViewWithName
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager::doHasWithName
     */
    public function testHasViewWithName()
    {
        $schema = new Schema();

        $view = new View();

        $view->setName('testView');

        $this->assertFalse(
            $this->schemaManager->hasViewWithName($schema, 'testView')
        );

        $schema->addView($view);

        $this->assertTrue(
            $this->schemaManager->hasViewWithName($schema, 'testView')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->schemaManager = new SchemaManager();
    }
}
