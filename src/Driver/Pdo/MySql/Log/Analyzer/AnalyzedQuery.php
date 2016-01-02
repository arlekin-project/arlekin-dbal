<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

class AnalyzedQuery
{
    /**
     * @var array
     */
    private $tables;
    
    /**
     * @var array
     */
    private $columnsByTable;
    
    /**
     * @var array
     */
    private $tableByAliasIndex;
    
    /**
     * @var Parent
     */
    private $analyzedParentQuery;
    
    /**
     * Construct.
     */
    public function __construct()
    {
        $this->tables = [];
        $this->columnsByTable = [];
        $this->tableByAliasIndex = [];
    }
    
    /**
     * @param string $table
     */
    public function addTable($table)
    {
        $this->tables[$table] = $table;
        
        return $this;
    }
    
    /**
     * @param string $table
     * @param string $column
     */
    public function addColumn($table, $column)
    {
        $this->columnsByTable[$table][$column] = $column;
        
        return $this;
    }
    
    /**
     * @param string $alias
     * @param string $table
     * 
     * @return AnalyzedQuery
     */
    public function addTableByAliasIndex($alias, $table)
    {
        $this->tableByAliasIndex[$alias] = $table;
        
        return $this;
    }
    
    /**
     * @param AnalyzedQuery $analyzedParentQuery
     * 
     * @return AnalyzedQuery
     */
    public function setParentQuery(AnalyzedQuery $analyzedParentQuery)
    {
        $this->analyzedParentQuery = $analyzedParentQuery;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * @return array
     */
    public function getColumnsByTable()
    {
        return $this->columnsByTable;
    }
    
    /**
     * @return array
     */
    public function getTableByAliasIndex()
    {
        return $this->tableByAliasIndex;
    }

    /**
     * @return AnalyzedQuery
     */
    public function getAnalyzedParentQuery()
    {
        return $this->analyzedParentQuery;
    }
}
