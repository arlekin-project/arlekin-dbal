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
}
