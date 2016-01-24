<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\ObjectQueryAnalyzeResult;

class MissingIndexAnalyzer
{
    /**
     * @param ObjectQueryAnalyzeResult $objectQueryAnalysisResult
     * @param Schema $schema
     *
     * @return MissingIndexAnalyzeResult
     */
    public function analyze(ObjectQueryAnalyzeResult $objectQueryAnalysisResult, Schema $schema)
    {
        $result = $objectQueryAnalysisResult->getQuery();

        $columnsByTable = $result->getColumnsByTable();

        return new MissingIndexAnalyzeResult();
    }
}
