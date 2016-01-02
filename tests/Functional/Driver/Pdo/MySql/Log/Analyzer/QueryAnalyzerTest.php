<?php

namespace Arlekin\Dbal\Tests\Functional\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\QueryAnalysisResult;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\QueryAnalyzer;
use Arlekin\Dbal\Tests\Functional\Driver\Pdo\MySql\BasePdoMySqlFunctionalTest;

class QueryAnalyzerTest extends BasePdoMySqlFunctionalTest
{
    protected function doAssertTestSimpleSelectResult(QueryAnalysisResult $result)
    {
        //Table Foo
        $schema = $result->getSchema();
        
        $tables = $schema->getTables();
        
        $this->assertCount(1, $tables);
        $this->assertSame('Foo', $tables[0]->getName());
        
        //One id column in table Foo
        $fooTable = $tables[0];
        
        /* @var $fooTable Table */
        
        $columns = $fooTable->getColumns();
        
        $this->assertCount(1, $columns);
        $this->assertSame('id', $columns[0]->getName());
    }
    
    public function testSelect()
    {                  
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT id FROM Foo', []);
        
        $this->doAssertTestSimpleSelectResult($result);
    }
    
    public function testSelectAliased()
    {   
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT f.id FROM Foo f', []);
        
        $this->doAssertTestSimpleSelectResult($result);
    }
    
    public function testSelectSubQuery()
    {                  
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT id, (SELECT id FROM Bar) AS id FROM Foo', []);
        
        $schema = $result->getSchema();
        
        $tables = $schema->getTables();
        
        //Two table
        $this->assertCount(2, $tables);
        
        //Table Foo
        $this->assertSame('Foo', $tables[0]->getName());
        
        $fooColumns = $tables[0]->getColumns();
        $this->assertCount(1, $fooColumns);
        
        //Table Bar
        $this->assertSame('Bar', $tables[1]->getName());
        
        $barColumns = $tables[1]->getColumns();
        
        $this->assertCount(1, $barColumns);
        
        //One id column in table Bar
        $this->assertSame('id', $barColumns[0]->getName());
    }
    
    public function testSelectNoFrom()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT 1', []);
        
        $schema = $result->getSchema();
        
        $tables = $schema->getTables();
        
        //No table
        $this->assertCount(0, $tables);
    }
    
    public function testJoin()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT f.id FROM Foo f INNER JOIN Bar b ON b.foobar_id = f.other_column', []);
        
        //Two tables
        
        $schema = $result->getSchema();
        
        $tables = $schema->getTables();
        
        $this->assertCount(2, $tables);
        
        //Table Foo
        $this->assertSame('Foo', $tables[0]->getName());
        
        //Table Bar
        $this->assertSame('Bar', $tables[1]->getName());
        
        $fooColumns = $tables[0]->getColumns();
        
        //One other_column column in table Foo
        $this->assertSame('other_column', $fooColumns[0]->getName());
        
        //One id column in table Foo
        $this->assertSame('id', $fooColumns[1]->getName());
        
        $barColumns = $tables[1]->getColumns();
        
        //One foobar_id column in table Bar
        $this->assertSame('foobar_id', $barColumns[0]->getName());
    }
    
    public function testTwoJoins()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT f.id FROM Foo f INNER JOIN Bar b ON b.foobar_id = f.other_column INNER JOIN Baz ba ON ba.foobar_id = f.other_column2', []);
        
        //Three tables
        
        $schema = $result->getSchema();
        
        $tables = $schema->getTables();
        
        $this->assertCount(3, $tables);
        
        //Table Foo
        $this->assertSame('Foo', $tables[0]->getName());
        
        //Table Bar
        $this->assertSame('Bar', $tables[1]->getName());
        
        //Table Baz
        $this->assertSame('Baz', $tables[2]->getName());
        
        $fooColumns = $tables[0]->getColumns();
        
        //One other_column column in table Foo
        $this->assertSame('other_column', $fooColumns[0]->getName());
        
        //One other_column2 column in table Foo
        $this->assertSame('other_column2', $fooColumns[1]->getName());
        
        //One id column in table Foo
        $this->assertSame('id', $fooColumns[2]->getName());
        
        $barColumns = $tables[1]->getColumns();
        
        //One foobar_id column in table Bar
        $this->assertSame('foobar_id', $barColumns[0]->getName());
    }
    
    public function testJoinTwoConditions()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT f.id FROM Foo f INNER JOIN Bar b ON b.foobar_id = f.other_column OR b.foobar_id = f.other_column2', []);
        
        //Two tables
        
        $schema = $result->getSchema();
        
        $tables = $schema->getTables();
        
        $this->assertCount(2, $tables);
        
        //Table Foo
        $this->assertSame('Foo', $tables[0]->getName());
        
        //Table Bar
        $this->assertSame('Bar', $tables[1]->getName());
        
        $fooColumns = $tables[0]->getColumns();
        
        //One other_column column in table Foo
        $this->assertSame('other_column', $fooColumns[0]->getName());
        
        //One other_column2 column in table Foo
        $this->assertSame('other_column2', $fooColumns[1]->getName());
        
        //One id column in table Foo
        $this->assertSame('id', $fooColumns[2]->getName());
        
        $barColumns = $tables[1]->getColumns();
        
        //One foobar_id column in table Bar
        $this->assertSame('foobar_id', $barColumns[0]->getName());
    }
    
    public function testJoinSubQueryCondition()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT f.id FROM Foo f INNER JOIN Bar b ON b.foobar_id = (SELECT f2.other_column FROM Foo f2)', []);
        
        //Two tables
        
        $schema = $result->getSchema();
        
        $tables = $schema->getTables();
        
        $this->assertCount(2, $tables);
        
        //Table Foo
        $this->assertSame('Foo', $tables[0]->getName());
        
        $fooColumns = $tables[0]->getColumns();
        
        //Table Bar
        $this->assertSame('Bar', $tables[1]->getName());
        
        $barColumns = $tables[1]->getColumns();
        
        //One other_column column in table Foo
        $this->assertSame('other_column', $fooColumns[0]->getName());
        
        //One id column in table Foo
        $this->assertSame('id', $fooColumns[1]->getName());
        
        //One foobar_id column in table Bar
        $this->assertSame('foobar_id', $barColumns[0]->getName());
    }
    
    protected function doTestWhere(QueryAnalysisResult $result)
    {
        $schema = $result->getSchema();
        
        $tables = $schema->getTables();
        
        //One table
        $this->assertCount(1, $tables);
        
        //Table Foo
        $this->assertSame('Foo', $tables[0]->getName());
        
        $fooColumns = $tables[0]->getColumns();
        $this->assertCount(2, $fooColumns);
        
        //One baz column in table Foo
        $this->assertSame('baz', $fooColumns[0]->getName());
        
        //One id column in table Foo
        $this->assertSame('id', $fooColumns[1]->getName());
    }
    
    public function testWhere()
    {                  
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT id FROM Foo WHERE baz = :foo', []);
        
        $this->doTestWhere($result);
    }
    
    public function testWhereConst()
    {                  
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT id FROM Foo WHERE baz = 1', []);
        
        $this->doTestWhere($result);
    }
}
