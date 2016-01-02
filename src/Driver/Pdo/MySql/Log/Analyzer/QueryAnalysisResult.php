<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

class QueryAnalysisResult
{
    /**
     * @var AnalyzedQuery
     */
    private $analyzedQuery;
    
    /**
     * @param AnalyzedQuery $analyzedQuery
     */
    public function __construct(AnalyzedQuery $analyzedQuery)
    {
        $this->analyzedQuery = $analyzedQuery;
    }
    
    /**
     * @return AnalyzedQuery
     */
    public function getAnalyzedQuery()
    {
        return $this->analyzedQuery;
    }
}
