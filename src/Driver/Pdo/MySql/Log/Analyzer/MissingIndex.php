<?php

namespace Calam\Dbal\Driver\Pdo\MySql\Log\Analyzer;

class MissingIndex
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var array
     */
    private $columns;

    /**
     * @param string $table
     * @param array $columns
     */
    public function __construct($table, array $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $table
     *
     * @return MissingIndex
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     *
     * @return MissingIndex
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }
}