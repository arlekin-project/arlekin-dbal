<?php

namespace Arlekin\Dbal\Tests\Functional\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\MissingIndexAnalyzer;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\MissingIndexAnalyzeResult;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\ObjectQueryAnalyzer;
use Arlekin\Dbal\Tests\Functional\Driver\Pdo\MySql\BasePdoMySqlFunctionalTest;

class MissingIndexAnalyzerTest extends BasePdoMySqlFunctionalTest
{
    public function testAnalyzeNoProblem()
    {
        $schema = new Schema();

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT 1;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $this->assertEmpty($result->getMissingIndexes());
    }

    public function testAnalyzeOneMissingIndex()
    {
        $schema = new Schema();

        $objectQueryAnalyzer = new ObjectQueryAnalyzer();

        $objectQueryAnalyzeResult = $objectQueryAnalyzer->analyze('SELECT id FROM Foo WHERE foo;');

        $missingIndexAnalyzer = new MissingIndexAnalyzer();

        $result = $missingIndexAnalyzer->analyze($objectQueryAnalyzeResult, $schema);

        $this->assertInstanceOf(MissingIndexAnalyzeResult::class, $result);

        $this->assertEmpty($result->getMissingIndexes());
    }
}
