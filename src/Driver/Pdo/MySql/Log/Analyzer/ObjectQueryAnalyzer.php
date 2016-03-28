<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer\Exception\InvalidSqlQuery;
use Arlekin\Dbal\Exception\DbalException;
use PHPSQLParser\PHPSQLParser;

class ObjectQueryAnalyzer
{
    /**
     * @var ObjectQuery
     */
    private $query;

    /**
     * @param string $query
     * @param array $parameters
     *
     * @return ObjectQueryAnalyzeResult
     *
     * @throws DbalException
     */
    public function analyze($query, array $parameters = [])
    {
        $parser = new PHPSQLParser();

        $parsedQuery = $parser->parse($query);

        if (false === $parsedQuery) {
            throw new InvalidSqlQuery(
                sprintf(
                    'Invalid SQL query "%s".',
                    $query
                )
            );
        }

        return $this->analyzeParsedQuery($parsedQuery);
    }

    /**
     * Whether or not given expression is a reference to a column.
     * An expression is considered to be a reference to a column
     * only if its expr_type is colref AND it doesn't start with : or ?
     *
     * @param array $expr
     *
     * @return bool
     */
    private function isColRef(array $expr)
    {
        return 'colref' === $expr['expr_type']
            && 0 !== strpos($expr['base_expr'], ':')
            && 0 !== strpos($expr['base_expr'], '?');
    }

    /**
     * @param array $expr
     *
     * @return array
     *
     * @throws DbalException
     */
    private function getTableAndColumnNamesFromExpr(array $expr)
    {
        $tables = $this->query->getTables();

        $exprType = $expr['expr_type'];

        if (!$this->isColRef($expr)) {
            throw new DbalException(sprintf('Expecting colref, got %s.', $exprType));
        }

        if ('*' === $expr['base_expr']) {
            $table = array_values($tables)[0];

            return [ $table, '*' ];
        }

        $columnNoQuotesPartsCount = count($expr['no_quotes']['parts']);

        //Column name is always the last element in exploded expression
        $column = $expr['no_quotes']['parts'][$columnNoQuotesPartsCount - 1];

        $tableByAliasIndex = $this->query->getTableByAliasIndex();

        if (1 === $columnNoQuotesPartsCount) {
            $table = array_values($tables)[0];
        } else {
            //Alias is always right before column name, which is the last element
            $alias = $expr['no_quotes']['parts'][$columnNoQuotesPartsCount - 2];

            $table = $tables[$tableByAliasIndex[$alias]];
        }

        if (!isset($table)) {
            throw new DbalException(sprintf('Missing table name for column %s.', $expr['base_expr']));
        }

        return [ $table, $column ];
    }

    private function addColumnFromColRef(array $expr)
    {
        $tableAndColumn = $this->getTableAndColumnNamesFromExpr($expr);

        $this->query->addColumn($tableAndColumn[0], $tableAndColumn[1]);

        return $tableAndColumn;
    }

    private function mergeWithSubQuery(array $subQuery)
    {
        $queryAnalyzer = new ObjectQueryAnalyzer();

        $queryAnalysisResult = $queryAnalyzer->analyzeParsedQuery($subQuery);

        $analyzedQuery = $queryAnalysisResult->getQuery();

        $analyzedQuery->setParentQuery($this->query);

        $tables = $analyzedQuery->getTables();

        $columnsByTable = $analyzedQuery->getColumnsByTable();

        $tableByAliasIndex = $analyzedQuery->getTableByAliasIndex();

        $columnsInWhereByTable = $analyzedQuery->getColumnsInWhereByTable();

        foreach ($tables as $table) {
            $this->query->addTable($table);

            unset($table);
        }

        foreach ($columnsByTable as $table => $columns) {
            foreach ($columns as $column) {
                $this->query->addColumn($table, $column);

                unset($column);
            }

            unset($table, $columns);
        }

        foreach ($tableByAliasIndex as $alias => $table) {
            $this->query->addTableByAliasIndex($alias, $table);

            unset($alias, $table);
        }

        foreach ($columnsInWhereByTable as $table => $columnsInWhere) {
            foreach ($columnsInWhere as $columns) {
                $this->query->addColumnsInWhereByTable($table, $columns);

                unset($columns);
            }

            unset($table, $columnsInWhere);
        }
    }

    private function addColumnFromWhereLikeExpr(array $whereLikeExpr)
    {
        $columnsByTable = [];

        foreach ($whereLikeExpr as $clause) {
            $exprType = $clause['expr_type'];

            if ('operator' === $exprType) {
                continue;
            } elseif ('subquery' === $exprType) {
                $this->mergeWithSubQuery($clause['sub_tree']);
            } elseif ($this->isColRef($clause)) {
                list($table, $column) = $this->addColumnFromColRef($clause);

                $columnsByTable[$table][] = $column;
            }

            unset($clause);
        }

        foreach ($columnsByTable as $table => $columns) {
            $this->query->addColumnsInWhereByTable($table, $columns);
        }
    }

    private function throwUnsupportedRefClause($refClause)
    {
        throw new DbalException(
            sprintf('Unsupported ref clause: %s', json_encode($refClause))
        );
    }

    private function doAnalyseParsedFrom(array $parsedFroms)
    {
        foreach ($parsedFroms as $parsedFrom) {
            $exprType = $parsedFrom['expr_type'];

            if ('table' === $exprType) {
                $table = $parsedFrom['table'];

                if (false !== $parsedFrom['alias']) {
                    $tableAlias = $parsedFrom['alias']['name'];

                    if (isset($this->tableNameAliasIndex[$tableAlias])) {
                        throw new DbalException(
                            sprintf(
                                'Alias %s already in use for table %s. Cannot use it for table %s.',
                                $tableAlias,
                                $this->tableNameAliasIndex[$tableAlias],
                                $table
                            )
                        );
                    }

                    $this->query->addTableByAliasIndex($tableAlias, $table);
                }

                $this->query->addTable($table);

                $refClauses = $parsedFrom['ref_clause'];

                if (false !== $refClauses) {
                    $this->addColumnFromWhereLikeExpr($refClauses);
                }
            } else {
                throw new DbalException(
                    sprintf('Unsupported expression type: %s', $exprType)
                );
            }

            unset($parsedFrom);
        }
    }

    private function doAnalyseParsedSelect(array $parsedSelects)
    {
        foreach ($parsedSelects as $parsedSelect) {
            $exprType = $parsedSelect['expr_type'];

            if ('const' === $exprType) {
                continue;
            } elseif ('expression' === $exprType) {
                if (count($parsedSelect['sub_tree']) > 1) {
                    throw new DbalException('Unsupported.');
                }

                $this->mergeWithSubQuery($parsedSelect['sub_tree'][0]['sub_tree']);
            } elseif ($this->isColRef($parsedSelect)) {
                $this->addColumnFromColRef($parsedSelect);
            } else {
                throw new DbalException(
                    sprintf('Unsupported expression type: %s', $exprType)
                );
            }

            unset($parsedSelect);
        }
    }

    private function doAnalyseParsedWhere(array $parsedWheres)
    {
        $this->addColumnFromWhereLikeExpr($parsedWheres);
    }

    private function analyzeParsedQuery(array $parsedQuery)
    {
        $this->query = new ObjectQuery();

        if (isset($parsedQuery['FROM'])) {
            $this->doAnalyseParsedFrom($parsedQuery['FROM']);
        }

        if (isset($parsedQuery['WHERE'])) {
            $this->doAnalyseParsedWhere($parsedQuery['WHERE']);
        }

        $this->doAnalyseParsedSelect($parsedQuery['SELECT']);

        return new ObjectQueryAnalyzeResult($this->query);
    }
}
