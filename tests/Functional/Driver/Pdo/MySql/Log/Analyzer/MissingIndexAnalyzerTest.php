<?php

namespace Arlekin\Dbal\Tests\Functional\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Index;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\MissingIndex;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\MissingIndexAnalyzer;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\MissingIndexAnalyzeResult;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\ObjectQueryAnalyzer;
use Arlekin\Dbal\Tests\Functional\Driver\Pdo\MySql\BasePdoMySqlFunctionalTest;

class MissingIndexAnalyzerTest extends BasePdoMySqlFunctionalTest
{
    public function testAnalyzeNoColumnNoProblem()
    {
        $schema = new Schema();

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT 1;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $this->assertEmpty($result->getMissingIndexes());
    }

    public function testAnalyzeOneColumnMissingIndex()
    {
        $schema = new Schema();

        $table = new Table();

        $table->setName('Foo');

        $schema->addTable($table);

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT id FROM Foo AS f WHERE f.foo = 42;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $missingIndexes = $result->getMissingIndexes();

        $this->assertCount(1, $missingIndexes);

        $missingIndex = $missingIndexes[0];

        /* @var $missingIndex MissingIndex */

        $this->assertSame('Foo', $missingIndex->getTable());

        $this->assertSame(
            [
                'foo',
            ],
            $missingIndex->getColumns()
        );
    }

    public function testAnalyzeTwoColumnsMissingIndex()
    {
        $schema = new Schema();

        $table = new Table();

        $table->setName('Foo');

        $schema->addTable($table);

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT id FROM Foo AS f WHERE f.foo = 42 AND f.bar = 43;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $missingIndexes = $result->getMissingIndexes();

        $this->assertCount(1, $missingIndexes);

        $missingIndex = $missingIndexes[0];

        /* @var $missingIndex MissingIndex */

        $this->assertSame('Foo', $missingIndex->getTable());

        $this->assertSame(
            [
                'bar',
                'foo',
            ],
            $missingIndex->getColumns()
        );
    }

    public function testAnalyzeOneColumnNoMissingIndex()
    {
        $schema = new Schema();

        $fooTable = new Table();

        $fooTable->setName('Foo');

        $fooColumn = new Column();

        $fooColumn->setName('foo');

        $fooTable->addColumn($fooColumn);

        $index = new Index();

        $index->addColumn($fooColumn);

        $fooTable->addIndex($index);

        $schema->addTable($fooTable);

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT id FROM Foo AS f WHERE f.foo = 42;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $missingIndexes = $result->getMissingIndexes();

        $this->assertCount(0, $missingIndexes);
    }

    public function testAnalyzeTwoColumnsNoMissingIndex()
    {
        $schema = new Schema();

        $fooTable = new Table();

        $fooTable->setName('Foo');

        $fooColumn = new Column();

        $fooColumn->setName('foo');

        $fooTable->addColumn($fooColumn);

        $barColumn = new Column();

        $barColumn->setName('bar');

        $fooTable->addColumn($barColumn);

        $index = new Index();

        $index->addColumn($fooColumn);
        $index->addColumn($barColumn);

        $fooTable->addIndex($index);

        $schema->addTable($fooTable);

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT id FROM Foo AS f WHERE f.foo = 42 AND f.bar = 43;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $missingIndexes = $result->getMissingIndexes();

        $this->assertCount(0, $missingIndexes);
    }

    public function testAnalyzeTwoTableOneColumnInEachNoMissingIndex()
    {
        $schema = new Schema();

        $fooTable = new Table();

        $fooTable->setName('Foo');

        $barTable = new Table();

        $barTable->setName('Bar');

        $fooColumn = new Column();

        $fooColumn->setName('foo');

        $fooTable->addColumn($fooColumn);

        $barColumn = new Column();

        $barColumn->setName('bar');

        $barTable->addColumn($barColumn);

        $fooIndex = new Index();

        $fooIndex->addColumn($fooColumn);

        $fooTable->addIndex($fooIndex);

        $barIndex = new Index();

        $barIndex->addColumn($barColumn);

        $barTable->addIndex($barIndex);

        $schema->addTable($fooTable);
        $schema->addTable($barTable);

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT id FROM Foo AS f, Bar AS b WHERE f.foo = 42 AND b.bar = 43;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $missingIndexes = $result->getMissingIndexes();

        $this->assertCount(0, $missingIndexes);
    }

    public function testAnalyzeTwoTableTwoColumnsInEachNoMissingIndex()
    {
        $schema = new Schema();

        $fooTable = new Table();

        $fooTable->setName('Foo');

        $barTable = new Table();

        $barTable->setName('Bar');

        $fooColumn = new Column();

        $fooColumn->setName('foo');

        $fooTable->addColumn($fooColumn);

        $foo2Column = new Column();

        $foo2Column->setName('foo2');

        $fooTable->addColumn($foo2Column);

        $barColumn = new Column();

        $barColumn->setName('bar');

        $barTable->addColumn($barColumn);

        $bar2Column = new Column();

        $bar2Column->setName('bar2');

        $barTable->addColumn($bar2Column);

        $fooIndex = new Index();

        $fooIndex->addColumn($fooColumn);
        $fooIndex->addColumn($foo2Column);

        $fooTable->addIndex($fooIndex);

        $barIndex = new Index();

        $barIndex->addColumn($barColumn);
        $barIndex->addColumn($bar2Column);

        $barTable->addIndex($barIndex);

        $schema->addTable($fooTable);
        $schema->addTable($barTable);

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT id FROM Foo AS f, Bar AS b WHERE f.foo = 42 AND f.foo2 = 43 AND b.bar = 44 AND b.bar2 = 45;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $missingIndexes = $result->getMissingIndexes();

        $this->assertCount(0, $missingIndexes);
    }

    public function testAnalyzeJoinMissingIndexesJoinTwoTableOneIndexInEach()
    {
        $schema = new Schema();

        $fooTable = new Table();

        $fooTable->setName('Foo');

        $barTable = new Table();

        $barTable->setName('Bar');

        $schema->addTable($fooTable);
        $schema->addTable($barTable);

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT f.id FROM Foo AS f INNER JOIN Bar b ON b.id = f.bar_id;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $missingIndexes = $result->getMissingIndexes();

        $this->assertCount(2, $missingIndexes);

        /* @var $missingIndexes MissingIndex[] */

        $this->assertSame('Bar', $missingIndexes[0]->getTable());
        $this->assertSame([ 'id' ], $missingIndexes[0]->getColumns());

        $this->assertSame('Foo', $missingIndexes[1]->getTable());
        $this->assertSame([ 'bar_id' ], $missingIndexes[1]->getColumns());
    }

    public function testAnalyzeJoinMissingIndexesJoinTwoTableTwoMultiColumnsIndexesInEach()
    {
        $schema = new Schema();

        $fooTable = new Table();

        $fooTable->setName('Foo');

        $barTable = new Table();

        $barTable->setName('Bar');

        $schema->addTable($fooTable);
        $schema->addTable($barTable);

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT f.id FROM Foo AS f INNER JOIN Bar b ON b.id = f.bar_id AND b.id_2 = f.bar_id2;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $missingIndexes = $result->getMissingIndexes();

        $this->assertCount(2, $missingIndexes);

        /* @var $missingIndexes MissingIndex[] */

        $this->assertSame('Bar', $missingIndexes[0]->getTable());
        $this->assertSame([ 'id', 'id_2' ], $missingIndexes[0]->getColumns());

        $this->assertSame('Foo', $missingIndexes[1]->getTable());
        $this->assertSame([ 'bar_id', 'bar_id2' ], $missingIndexes[1]->getColumns());
    }

    public function testAnalyzeJoinNoMissingIndexJoinTwoTableOneIndexInEach()
    {
        $schema = new Schema();

        $fooTable = new Table();

        $fooTable->setName('Foo');

        $fooBarIdColumn = new Column();

        $fooBarIdColumn->setName('bar_id');

        $fooIndex = new Index();

        $fooIndex->addColumn($fooBarIdColumn);

        $fooTable->addColumn($fooBarIdColumn);

        $fooTable->addIndex($fooIndex);

        $barTable = new Table();

        $barTable->setName('Bar');

        $barIdColumn = new Column();

        $barIdColumn->setName('id');

        $barIndex = new Index();

        $barIndex->addColumn($barIdColumn);

        $barTable->addColumn($barIdColumn);

        $barTable->addIndex($barIndex);

        $schema->addTable($fooTable);
        $schema->addTable($barTable);

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT f.id FROM Foo AS f INNER JOIN Bar b ON b.id = f.bar_id;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $missingIndexes = $result->getMissingIndexes();

        $this->assertCount(0, $missingIndexes);
    }

    public function testAnalyzeJoinNoMissingIndexJoinTwoTableTwoMultiColumnsIndexesInEach()
    {
        $schema = new Schema();

        $fooTable = new Table();

        $fooTable->setName('Foo');

        $fooBarIdColumn = new Column();

        $fooBarIdColumn->setName('bar_id');

        $fooBarId2Column = new Column();

        $fooBarId2Column->setName('bar_id2');

        $fooIndex = new Index();

        $fooIndex->addColumn($fooBarIdColumn);
        $fooIndex->addColumn($fooBarId2Column);

        $fooTable->addColumn($fooBarIdColumn);
        $fooTable->addColumn($fooBarId2Column);

        $fooTable->addIndex($fooIndex);

        $barTable = new Table();

        $barTable->setName('Bar');

        $barIdColumn = new Column();

        $barIdColumn->setName('id');

        $barId2Column = new Column();

        $barId2Column->setName('id_2');

        $barIndex = new Index();

        $barIndex->addColumn($barIdColumn);
        $barIndex->addColumn($barId2Column);

        $barTable->addColumn($barIdColumn);
        $barTable->addColumn($barId2Column);

        $barTable->addIndex($barIndex);

        $schema->addTable($fooTable);
        $schema->addTable($barTable);

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT f.id FROM Foo AS f INNER JOIN Bar b ON b.id = f.bar_id AND b.id_2 = f.bar_id2;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $missingIndexes = $result->getMissingIndexes();

        $this->assertCount(0, $missingIndexes);
    }
}
