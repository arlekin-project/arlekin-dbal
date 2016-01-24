<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

class ObjectQuery
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
     * @var ObjectQuery
     */
    private $parentQuery;

    /**
     * @var array
     */
    private $childrenQueries;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->tables = [];
        $this->columnsByTable = [];
        $this->tableByAliasIndex = [];
        $this->childrenQueries = [];
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
     * @return ObjectQuery
     */
    public function addTableByAliasIndex($alias, $table)
    {
        $this->tableByAliasIndex[$alias] = $table;

        return $this;
    }

    /**
     * @param ObjectQuery $parentQuery
     *
     * @return ObjectQuery
     */
    public function setParentQuery(ObjectQuery $parentQuery)
    {
        $this->parentQuery = $parentQuery;

        $parentQuery->addChildQuery($this);

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
     * @return ObjectQuery
     */
    public function getAnalyzedParentQuery()
    {
        return $this->parentQuery;
    }

    /**
     * @return array
     */
    public function getAnalyzedChildrenQueries()
    {
        return $this->childrenQueries;
    }

    /**
     * @param ObjectQuery $childQuery
     *
     * @return ObjectQuery
     */
    public function addChildQuery(ObjectQuery $childQuery)
    {
        $this->childrenQueries[] = $childQuery;

        return $this;
    }
}
