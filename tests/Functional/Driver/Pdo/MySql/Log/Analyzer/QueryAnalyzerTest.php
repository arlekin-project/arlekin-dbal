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
        
        $this->assertNull($query->getAnalyzedParentQuery());
        
        $childrenQueries = $query->getAnalyzedChildrenQueries();
        
        $this->assertCount(1, $childrenQueries);
        
        $this->assertSame($query, $childrenQueries[0]->getAnalyzedParentQuery());
        
        $this->assertTables([ 'Bar', ], $childrenQueries[0]);
        
        $this->assertColumns([ 'id', ], $childrenQueries[0], 'Bar');
    }
    
    public function testSelectNoFrom()
    {
        $analyzer = new QueryAnalyzer();
        
        $result = $analyzer->analyze('SELECT 1', []);
        
        $query = $result->getAnalyzedQuery();
        
        $tables = $query->getTables();
        
        $this->assertCount(0, $tables);
        
        $this->assertNull($query->getAnalyzedParentQuery());
        $this->assertEmpty($query->getAnalyzedChildrenQueries());
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
        
        $this->assertNull($query->getAnalyzedParentQuery());
        $this->assertEmpty($query->getAnalyzedChildrenQueries());
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
        
        $this->assertNull($query->getAnalyzedParentQuery());
        $this->assertEmpty($query->getAnalyzedChildrenQueries());
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
        
        $this->assertNull($query->getAnalyzedParentQuery());
        $this->assertEmpty($query->getAnalyzedChildrenQueries());
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
        
        $this->assertNull($query->getAnalyzedParentQuery());
        
        $childrenQueries = $query->getAnalyzedChildrenQueries();
        
        $this->assertSame($query, $childrenQueries[0]->getAnalyzedParentQuery());
        
        $this->assertCount(1, $childrenQueries);
        
        $this->assertTables([ 'Foo', ], $childrenQueries[0]);
        
        $this->assertColumns([ 'other_column', ], $childrenQueries[0], 'Foo');
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
    
    private function doAssertSameKeyValue(array $expected, array $actual)
    {
        $this->assertSame($expected, array_values($actual));
        $this->assertSame($expected, array_keys($actual));
    }
    
    private function assertTables(array $expected, AnalyzedQuery $query)
    {        
        $actual = $query->getTables();
        
        $this->doAssertSameKeyValue($expected, $actual);
    }
    
    private function assertColumns(array $expected, AnalyzedQuery $query, $table)
    {
        $columnsByTable = $query->getColumnsByTable();
    
        $columns = $columnsByTable[$table];
        
        $this->doAssertSameKeyValue($expected, $columns);
    }
    
    private function doAssertTestSimpleSelectResult(QueryAnalysisResult $result)
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
                'id',
            ],
            $query,
            'Foo'
        );
        
        $this->assertNull($query->getAnalyzedParentQuery());
        $this->assertEmpty($query->getAnalyzedChildrenQueries());
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
        
        $this->assertNull($query->getAnalyzedParentQuery());
        $this->assertEmpty($query->getAnalyzedChildrenQueries());
    }
}