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
        $query = $objectQueryAnalysisResult->getQuery();

        $columnsInWhereByTable = $query->getColumnsInWhereByTable();

        $missingIndexes = [];

        $missingIndexResult = new MissingIndexAnalyzeResult();

        foreach ($columnsInWhereByTable as $tableName => $columnsInWhere) {
            if (!$schema->hasTableWithName($tableName)) {
                throw new \Arlekin\Dbal\Exception\DbalException(
                    sprintf('Missing table with name: %s.', $tableName)
                );
            }

            $table = $schema->getTableWithName($tableName);

            $indexes = $table->getIndexes();

            $indexesByConcatColumns = [];

            foreach ($columnsInWhere as $columns) {
                sort($columns);

                $concatColumns = implode(',', $columns);

                if (!isset($indexesByConcatColumns[$concatColumns])) {
                    $missingIndexes[] = new MissingIndex($table->getName(), $columns);
                }
            }
        }

        $missingIndexResult->setMissingIndexes($missingIndexes);

        return $missingIndexResult;
    }
}
