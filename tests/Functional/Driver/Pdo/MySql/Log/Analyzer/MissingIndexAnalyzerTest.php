<?php

namespace Arlekin\Dbal\Tests\Functional\Driver\Pdo\MySql\Log\Analyzer;

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
}
