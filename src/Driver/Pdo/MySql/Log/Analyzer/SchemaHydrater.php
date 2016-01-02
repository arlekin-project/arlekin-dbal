<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Exception\DbalException;

class SchemaHydrater
{
    /**
     * @var array
     */
    protected $tablesByName;
    
    /**
     * @var array
     */
    protected $tableNameAliasIndex;
    
    /**
     * @var array
     */
    protected $columnsByTableNameByName;
    
    /**
     * @var Schema
     */
    protected $schema;
    
    protected function rebuildIndexes()
    {
        $tables = $this->schema->getTables();
        
        $this->tablesByName = [];
        $this->columnsByTableNameByName = [];
        
        foreach ($tables as $table) {
            $tableName = $table->getName();
            $tableColumns = $table->getColumns();
            
            $this->tablesByName[$tableName] = $table;
            
            foreach ($tableColumns as $column) {
                $columnName = $column->getName();
                
                $this->columnsByTableNameByName[$tableName][$columnName] = $column;
            }
        }
    }
    
    /**
     * @param string $tableName
     * 
     * @return Table
     */
    protected function getTableByName($tableName)
    {
        return $this->tablesByName[$tableName];
    }
    
    protected function addColumnToTable($tableName, $columnName)
    {
        $table = $this->getTableByName($tableName);
        
        if (!$table->hasColumnWithName($columnName)) {
            $column = new Column();
                
            $column->setName($columnName);

            $table->addColumn($column);
            
            $this->columnsByTableNameByName[$table->getName()][$columnName] = $column;
        }
    }
    
    protected function addColumnFromColRef(array $expr)
    {        
        $exprType = $expr['expr_type'];
        
        if (!$this->isColRef($expr)) {
            throw new DbalException(sprintf('Expecting colref, got %s.', $exprType));
        }
            
        $columnNoQuotesPartsCount = count($expr['no_quotes']['parts']);
        
        //Column name is always the last element in exploded expression
        $columnName = $expr['no_quotes']['parts'][$columnNoQuotesPartsCount - 1];
        
        if (1 === $columnNoQuotesPartsCount) {
            $table = array_values($this->tablesByName)[0];
        } else {
            //Alias is always right before column name, which is the last element
            $alias = $expr['no_quotes']['parts'][$columnNoQuotesPartsCount - 2];
            
            $table = $this->tablesByName[$this->tableNameAliasIndex[$alias]];
        }

        if (!isset($table)) {
            throw new DbalException(sprintf('Missing table name for column %s.', $expr['base_expr']));
        }

        /* @var $table Table */

        $this->addColumnToTable($table->getName(), $columnName);
    }
    
    protected function mergeWithSubQuery(array $subQuery)
    {
        $hydrater = new SchemaHydrater();
                
        $otherSchema = $hydrater->hydrate($subQuery);

        $this->mergeSchemas($otherSchema);
    }
    
    protected function doMergeSchemas(array $tables, array &$mergedTables)
    {        
        foreach ($tables as $table) {
            /* @var $table Table */
            
            $tableName = $table->getName();
            $columns = $table->getColumns();
            
            if (!isset($mergedTables[$tableName])) {
                $mergedTable = new Table();
                
                $mergedTable->setName($tableName);
                
                $mergedTables[$tableName] = $mergedTable;
            } else {
                $mergedTable = $mergedTables[$tableName];
            }
            
            foreach ($columns as $column) {                
                if (!$mergedTable->hasColumnWithName($column->getName())) {
                    $mergedTable->addColumn($column);
                }
            }
        }
    }
    
    protected function mergeSchemas(Schema $otherSchema)
    {
        $tables = $this->schema->getTables();
        
        $otherSchemaTables = $otherSchema->getTables();
                
        $mergedTables = [];
        
        $this->doMergeSchemas($tables, $mergedTables);
        $this->doMergeSchemas($otherSchemaTables, $mergedTables);
        
        $mergedSchema = new Schema();
        
        $mergedSchema->setTables(array_values($mergedTables));
        
        $this->schema = $mergedSchema;
        
        $this->rebuildIndexes();
    }
    
    protected function addColumnFromWhereLikeExpr(array $whereLikeExpr)
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
    
    protected function throwUnsupportedRefClause($refClause)
    {        
        throw new DbalException(
            sprintf('Unsupported ref clause: %s', json_encode($refClause))
        );
    }
    
    protected function doAnalyseParsedFrom(array $parsedFroms)
    {
        foreach ($parsedFroms as $parsedFrom) {
            $exprType = $parsedFrom['expr_type'];
            
            if ('table' === $exprType) {
                $tableName = $parsedFrom['table'];
                
                if (false !== $parsedFrom['alias']) {
                    $tableAlias = $parsedFrom['alias']['name'];
                    
                    if (isset($this->tableNameAliasIndex[$tableAlias])) {
                        throw new DbalException(
                            sprintf(
                                'Alias %s already in use for table %s. Cannot use it for table %s.',
                                $tableAlias,
                                $this->tableNameAliasIndex[$tableAlias],
                                $tableName
                            )
                        );
                    }
                
                    $this->tableNameAliasIndex[$tableAlias] = $tableName;
                }
                
                if (!isset($this->tablesByName[$tableName])) {
                    $table = new Table();
                    
                    $this->schema->addTable($table);
                    
                    $table->setName($tableName);
                    
                    $this->tablesByName[$tableName] = $table;
                } else {
                    $table = $this->tablesByName[$tableName];
                }
                
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
    
    protected function doAnalyseParsedSelect(array $parsedSelects)
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
    
    /**
     * Whether or not given expression is a reference to a column.
     * An expression is considered to be a reference to a column
     * only if its expr_type is colref AND it doesn't start with : or ?
     * 
     * @param array $expr
     * 
     * @return bool
     */
    protected function isColRef(array $expr)
    {
        return 'colref' === $expr['expr_type']
            && 0 !== strpos($expr['base_expr'], ':')
            && 0 !== strpos($expr['base_expr'], '?');
    }
    
    protected function doAnalyseParsedWhere(array $parsedWheres)
    {
        foreach ($parsedWheres as $parsedWhere) {            
            if ($this->isColRef($parsedWhere)) {
                $this->addColumnFromColRef($parsedWhere);
            }
        }
    }
    
    protected function parseQuery(array $parsedQuery)
    {
        if (isset($parsedQuery['FROM'])) {
            $this->doAnalyseParsedFrom($parsedQuery['FROM']);
        }
        
        if (isset($parsedQuery['WHERE'])) {
            $this->doAnalyseParsedWhere($parsedQuery['WHERE']);
        }
        
        $this->doAnalyseParsedSelect($parsedQuery['SELECT']);
    }
    
    public function hydrate(array $parsed)
    {
        $this->schema = new Schema();
        
        $this->parseQuery($parsed);
        
        return $this->schema;
    }
}