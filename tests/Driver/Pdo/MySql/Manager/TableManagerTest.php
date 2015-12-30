<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Driver\Pdo\MySql;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ColumnType;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Index;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\PrimaryKey;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager;
use Arlekin\Dbal\Tests\Helper\CommonTestHelper;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class TableManagerTest extends AbstractBasePdoMySqlTest
{
    /**
     * @var TableManager
     */
    protected $tableManager;

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager::hasForeignKeyWithColumnsAndReferencedColumnsNamed
     */
    public function testHasForeignKeyWithColumnsAndReferencedColumnsNamed()
    {
        $testTable = new Table();

        $testTable->setName('test');

        $this->assertFalse(
            $this->tableManager->hasForeignKeyWithColumnsAndReferencedColumnsNamed(
                $testTable,
                [
                    'testColumnInTest',
                ],
                'test2',
                [
                    'testColumnInTest2',
                ]
            )
        );

        $testTable2 = new Table();

        $testTable2->setName('test2');

        $testColumnInTest = new Column();

        $testColumnInTest->setName('testColumnInTest');

        $testColumnInTest2 = new Column();

        $testColumnInTest2->setName('testColumnInTest2');

        $testTable->setColumns(
            [
                $testColumnInTest,
            ]
        );

        $testTable2->setColumns(
            [
                $testColumnInTest2,
            ]
        );

        $testForeignKey = new ForeignKey();

        $testForeignKey->setColumns(
            [
                $testColumnInTest,
            ]
        );

        $testForeignKey->setReferencedColumns(
            [
                $testColumnInTest2,
            ]
        );

        $testForeignKey->setReferencedTable($testTable2);

        $testTable->setForeignKeys(
            [
                $testForeignKey,
            ]
        );

        $this->assertTrue(
            $this->tableManager->hasForeignKeyWithColumnsAndReferencedColumnsNamed(
                $testTable,
                [
                    'testColumnInTest',
                ],
                'test2',
                [
                    'testColumnInTest2',
                ]
            )
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager::hasColumn
     */
    public function testHasColumn()
    {
        $table = new Table();

        $column = new Column();

        $this->assertFalse(
            $this->tableManager->hasColumn($table, $column)
        );

        $table->setColumns(
            [
                $column,
            ]
        );

        $this->assertTrue(
            $this->tableManager->hasColumn($table, $column)
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager::hasColumnWithName
     */
    public function testHasColumnWithName()
    {
        $table = new Table();

        $column = new Column();

        $column->setName('test');

        $this->assertFalse(
            $this->tableManager->hasColumnWithName($table, 'test')
        );

        $table->setColumns(
            [
                $column,
            ]
        );

        $this->assertTrue(
            $this->tableManager->hasColumnWithName($table, 'test')
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager::hasIndexWithName
     */
    public function testHasIndexWithName()
    {
        $table = new Table();

        $index = new Index();

        $index->setName('test');

        $this->assertFalse(
            $this->tableManager->hasIndexWithName($table, 'test')
        );

        $table->setIndexes(
            [
                $index,
            ]
        );

        $this->assertTrue(
            $this->tableManager->hasIndexWithName($table, 'test')
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager::hasPrimaryKeyWithColumnsNamed
     */
    public function testHasPrimaryKeyWithColumnsNamed()
    {
        $table = new Table();

        $column = new Column();
        $column2 = new Column();

        $column->setName('test');
        $column2->setName('test2');

        $primaryKey = new PrimaryKey();

        $primaryKey->setColumns(
            [
                $column,
                $column2,
            ]
        );

        $this->assertFalse(
            $this->tableManager->hasPrimaryKeyWithColumnsNamed(
                $table,
                [
                    'test',
                    'test2',
                ]
            )
        );

        $table->setPrimaryKey($primaryKey);

        $this->assertTrue(
            $this->tableManager->hasPrimaryKeyWithColumnsNamed(
                $table,
                [
                    'test',
                    'test2',
                ]
            )
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager::getIndexWithName
     */
    public function testGetIndexWithName()
    {
        $table = new Table();

        $table->setName('testTable');

        $index = new Index();

        $index->setName('test');

        $table->setIndexes(
            [
                $index,
            ]
        );

        $result = $this->tableManager->getIndexWithName($table, 'test');

        $this->assertSame($index, $result);
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager::getIndexWithName
     */
    public function testGetIndexWithNameExceptionIfTableHasNoIndexWithName()
    {
        $table = new Table();

        $table->setName('testTable');

        CommonTestHelper::assertExceptionThrown(
            function () use ($table) {
                $this->tableManager->getIndexWithName($table, 'test');
            },
            \Exception::class,
            'Table "testTable" has no index with name "test".'
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager::getColumnWithName
     */
    public function testGetColumnWithName()
    {
        $table = new Table();

        $table->setName('testTable');

        $column = new Column();

        $column->setName('test');

        $table->setColumns(
            [
                $column,
            ]
        );

        $result = $this->tableManager->getColumnWithName($table, 'test');

        $this->assertSame($column, $result);
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager::getColumnWithName
     */
    public function testGetColumnWithNameExceptionIfTableHasNoColumnWithName()
    {
        $table = new Table();

        $table->setName('testTable');

        CommonTestHelper::assertExceptionThrown(
            function () use ($table) {
                $this->tableManager->getColumnWithName($table, 'test');
            },
            \Exception::class,
            'Table "testTable" has no column with name "test".'
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager::columnsAreSameIgnoreAutoIncrement
     */
    public function testColumnsAreSameExceptAutoIncrement()
    {
        $initColumn1 = new Column();
        $initColumn2 = new Column();
        $table = new Table();

        $table->setColumns(
            [
                $initColumn1,
                $initColumn2,
            ]
        );

        $table->setName('testTable');

        $initColumn1->setType(ColumnType::TYPE_INT);
        $initColumn2->setType(ColumnType::TYPE_INT);

        $result = $this->tableManager->columnsAreSameIgnoreAutoIncrement($initColumn1, $initColumn2);

        $this->assertTrue($result);

        $column1 = clone $initColumn1;
        $column2 = clone $initColumn2;

        $column1->setAutoIncrement(true);
        $column2->setAutoIncrement(false);

        $result2 = $this->tableManager->columnsAreSameIgnoreAutoIncrement($column1, $column2);

        $this->assertTrue($result2);

        $column1 = clone $initColumn1;
        $column2 = clone $initColumn2;

        $column1->setName('test');
        $column2->setName('test2');

        $result3 = $this->tableManager->columnsAreSameIgnoreAutoIncrement($column1, $column2);

        $this->assertFalse($result3);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->tableManager = new TableManager();
    }
}
