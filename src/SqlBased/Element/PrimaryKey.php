<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\SqlBased\Element;

/**
 * Represents a SQL primary key.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
abstract class PrimaryKey
{
    /**
     * The table the primary key belongs to.
     *
     * @var Table
     */
    protected $table;

    /**
     * The primary key's columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->columns = [];
    }

    /**
     * Gets the primary key's table.
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Sets the primary key's table.
     *
     * @param Table $table
     *
     * @return PrimaryKey
     */
    public function setTable(
        Table $table = null
    ) {
        $this->table = $table;

        return $this;
    }

    /**
     * Gets the primary key's columns.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Sets the primary key's columns.
     *
     * @param array $columns
     *
     * @return PrimaryKey
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param Column $column
     *
     * @return PrimaryKey
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Converts the primary key into an array.
     *
     * @todo Move the toArray responsability away from the primary key.
     *
     * @return array
     */
    public function toArray()
    {
        $table = $this->getTable();
        $tableName = $table->getName();

        $columnsAsArray = array();

        foreach ($this->columns as $column) {
            $columnsAsArray[] = $column->getName();
        }

        $arr = array(
            'columns' => $columnsAsArray,
            'table' => $tableName
        );

        return $arr;
    }
}
