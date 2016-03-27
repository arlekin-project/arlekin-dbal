<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Index;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\ObjectQueryAnalyzeResult;
use Arlekin\Dbal\Exception\DbalException;

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
                throw new DbalException(
                    sprintf('Missing table with name: %s.', $tableName)
                );
            }

            $table = $schema->getTableWithName($tableName);

            $indexesByConcatColumns = [];

            $indexes = $table->getIndexes();

            foreach ($indexes as $index) {
                /* @var $index Index */

                $columns = $index->getColumns();

                $columnNames = [];

                foreach ($columns as $column) {
                    /* @var $column Column */

                    $columnNames[] = $column->getName();

                    unset($column);
                }

                sort($columnNames);

                $concatColumnNames = implode(',', $columnNames);

                $indexesByConcatColumns[$concatColumnNames] = $index;

                unset($index);
            }

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
