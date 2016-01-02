<?php

namespace Arlekin\Dbal\Tests\Functional\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\AnalyzedQuery;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\QueryAnalysisResult;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\QueryAnalyzer;
use Arlekin\Dbal\Tests\Functional\Driver\Pdo\MySql\BasePdoMySqlFunctionalTest;

class QueryAnalyzerTest extends BasePdoMySqlFunctionalTest
{
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
        
        $query = $result->getAnalyzedQuery();
        
        $this->assertTables(
            [
                'Foo',
                'Bar',
            ],
            $query
        );
        
        $this->assertColumns([ 'id', ], $query, 'Foo');
        $this->assertColumns([ 'id', ], $query, 'Bar');
    }
    
    public function testSelectNoFrom()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT 1', []);
        
        $query = $result->getAnalyzedQuery();
        
        $tables = $query->getTables();
        
        //No table
        $this->assertCount(0, $tables);
    }
    
    public function testJoin()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT f.id FROM Foo f INNER JOIN Bar b ON b.foobar_id = f.other_column', []);
        
        $query = $result->getAnalyzedQuery();
        
        $this->assertTables(
            [
                'Foo',
                'Bar',
            ],
            $query
        );
        
        $this->assertColumns(
            [
                'other_column',
                'id',
            ],
            $query,
            'Foo'
        );
        
        $this->assertColumns(
            [
                'foobar_id',
            ],
            $query,
            'Bar'
        );
    }
    
    public function testTwoJoins()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT f.id FROM Foo f INNER JOIN Bar b ON b.foobar_id = f.other_column INNER JOIN Baz ba ON ba.foobar_id = f.other_column2', []);
        
        $query = $result->getAnalyzedQuery();
        
        $this->assertTables(
            [
                'Foo',
                'Bar',
                'Baz',
            ],
            $query
        );
        
        $this->assertColumns(
            [
                'other_column',
                'other_column2',
                'id',
            ],
            $query,
            'Foo'
        );
        
        $this->assertColumns(
            [
                'foobar_id',
            ],
            $query,
            'Bar'
        );
    }
    
    public function testJoinTwoConditions()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT f.id FROM Foo f INNER JOIN Bar b ON b.foobar_id = f.other_column OR b.foobar_id = f.other_column2', []);
        
        $query = $result->getAnalyzedQuery();
        
        $this->assertColumns(
            [
                'other_column',
                'other_column2',
                'id',
            ],
            $query,
            'Foo'
        );
        
        $this->assertColumns(
            [
                'foobar_id',
            ],
            $query,
            'Bar'
        );
    }
    
    public function testJoinSubQueryCondition()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT f.id FROM Foo f INNER JOIN Bar b ON b.foobar_id = (SELECT f2.other_column FROM Foo f2)', []);
        
        
        $query = $result->getAnalyzedQuery();
        
        $this->assertTables(
            [
                'Foo',
                'Bar',
            ],
            $query
        );
        
        $this->assertColumns(
            [
                'other_column',
                'id',
            ],
            $query,
            'Foo'
        );
        
        $this->assertColumns(
            [
                'foobar_id',
            ],
            $query,
            'Bar'
        );
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
    
    private function assertTables(array $expected, AnalyzedQuery $query)
    {
        $actual = $query->getTables();
        
        $this->assertSame($expected, array_values($actual));
        $this->assertSame($expected, array_keys($actual));
    }
    
    private function assertColumns(array $expected, AnalyzedQuery $query, $table)
    {
        $columnsByTable = $query->getColumnsByTable();
        
        $columns = $columnsByTable[$table];
        
        $this->assertSame($expected, array_values($columns));
        $this->assertSame($expected, array_keys($columns));
    }
    
    private function doAssertTestSimpleSelectResult(QueryAnalysisResult $result)
    {
        //Table Foo
        $query = $result->getAnalyzedQuery();
        
        $this->assertTables(
            [
                'Foo',
            ],
            $query
        );
        
        $this->assertColumns(
            [
                'id',
            ],
            $query,
            'Foo'
        );
    }
    
    private function doTestWhere(QueryAnalysisResult $result)
    {
        $query = $result->getAnalyzedQuery();
        
        $this->assertTables(
            [
                'Foo',
            ],
            $query
        );
        
        $this->assertColumns(
            [
                'baz',
                'id',
            ],
            $query,
            'Foo'
        );
    }
}
