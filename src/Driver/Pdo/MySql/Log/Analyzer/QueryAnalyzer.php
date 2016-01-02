<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Exception\DbalException;
use PHPSQLParser\PHPSQLParser;

class QueryAnalyzer
{        
    /**
     * @var AnalyzedQuery
     */
    private $analyzedQuery;
    
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
    
    private function addColumnFromColRef(array $expr)
    {        
        $exprType = $expr['expr_type'];
        
        if (!$this->isColRef($expr)) {
            throw new DbalException(sprintf('Expecting colref, got %s.', $exprType));
        }
            
        $columnNoQuotesPartsCount = count($expr['no_quotes']['parts']);
        
        //Column name is always the last element in exploded expression
        $column = $expr['no_quotes']['parts'][$columnNoQuotesPartsCount - 1];
        
        $table = $this->analyzedQuery->getTables();
        $tableByAliasIndex = $this->analyzedQuery->getTableByAliasIndex();
        
        if (1 === $columnNoQuotesPartsCount) {
            $table = array_values($table)[0];
        } else {
            //Alias is always right before column name, which is the last element
            $alias = $expr['no_quotes']['parts'][$columnNoQuotesPartsCount - 2];
            
            $table = $table[$tableByAliasIndex[$alias]];
        }

        if (!isset($table)) {
            throw new DbalException(sprintf('Missing table name for column %s.', $expr['base_expr']));
        }

        $this->analyzedQuery->addColumn($table, $column);
    }
    
    private function mergeWithSubQuery(array $subQuery)
    {
        $queryAnalyzer = new QueryAnalyzer();
        
        $queryAnalysisResult = $queryAnalyzer->analyzeParsedQuery($subQuery);
        
        $analyzedQuery = $queryAnalysisResult->getAnalyzedQuery();
        
        $analyzedQuery->setParentQuery($this->analyzedQuery);
        
        $tables = $analyzedQuery->getTables();
        
        $columnsByTable = $analyzedQuery->getColumnsByTable();
        
        $tableByAliasIndex = $analyzedQuery->getTableByAliasIndex();
        
        foreach ($tables as $table) {
            $this->analyzedQuery->addTable($table);
        }
        
        foreach ($columnsByTable as $table => $columns) {
            foreach ($columns as $column) {
                $this->analyzedQuery->addColumn($table, $column);
            }
        }
        
        foreach ($tableByAliasIndex as $alias => $table) {
            $this->analyzedQuery->addTableByAliasIndex($alias, $table);
        }
    }
    
    private function addColumnFromWhereLikeExpr(array $whereLikeExpr)
    {
        foreach ($whereLikeExpr as $clause) {
            $exprType = $clause['expr_type'];
            
            if ('operator' === $exprType) {
                continue;
            } elseif ('subquery' === $exprType) {
                $this->mergeWithSubQuery($clause['sub_tree']);
            } elseif ($this->isColRef($clause)) {
                $this->addColumnFromColRef($clause);
            } else {
                $this->throwUnsupportedRefClause($clause);
            }
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
                    
                    $this->analyzedQuery->addTableByAliasIndex($tableAlias, $table);
                }
                
                $this->analyzedQuery->addTable($table);
                
                $refClauses = $parsedFrom['ref_clause'];
                
                if (false !== $refClauses) {
                    $this->addColumnFromWhereLikeExpr($refClauses);
                }
            } else {
                throw new DbalException(
                    sprintf('Unsupported expression type: %s', $exprType)
                );
            }
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
        }
    }
    
    private function doAnalyseParsedWhere(array $parsedWheres)
    {
        foreach ($parsedWheres as $parsedWhere) {            
            if ($this->isColRef($parsedWhere)) {
                $this->addColumnFromColRef($parsedWhere);
            }
        }
    }
    
    public function analyzeParsedQuery(array $parsedQuery)
    {
        $this->analyzedQuery = new AnalyzedQuery();
        
        if (isset($parsedQuery['FROM'])) {
            $this->doAnalyseParsedFrom($parsedQuery['FROM']);
        }
        
        if (isset($parsedQuery['WHERE'])) {
            $this->doAnalyseParsedWhere($parsedQuery['WHERE']);
        }
        
        $this->doAnalyseParsedSelect($parsedQuery['SELECT']);
        
        return new QueryAnalysisResult($this->analyzedQuery);
    }
    
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
        
        $parsedQuery = $parser->parse($query);
        
        return $this->analyzeParsedQuery($parsedQuery);
    }
}
