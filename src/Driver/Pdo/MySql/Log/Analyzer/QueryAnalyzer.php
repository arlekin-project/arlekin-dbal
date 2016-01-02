<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Exception\DbalException;
use PHPSQLParser\PHPSQLParser;

class QueryAnalyzer
{   
    /**
     * @param string $query
     * @param array $parameters
     * 
     * @return QueryAnalysisResult
     * 
     * @throws DbalException
     */
    public function analyze($query, array $parameters = [])
    {
        $parser = new PHPSQLParser();
        
        $parsed = $parser->parse($query);
        
        $hydrater = new SchemaHydrater();
        
        $schema = $hydrater->hydrate($parsed);
        
        return new QueryAnalysisResult($schema);
    }
}
