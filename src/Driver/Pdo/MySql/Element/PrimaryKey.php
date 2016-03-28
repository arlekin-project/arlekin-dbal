<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Element;

/**
 * Represents a MySQL primary key.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class PrimaryKey
{
    /**
     * The table the primary key belongs to.
     *
     * @var Table
     */
    private $table;

    /**
     * The primary key's columns.
     *
     * @var array
     */
    private $columns;

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
    public function setTable(Table $table = null)
    {
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
     * @param int $index
     *
     * @return PrimaryKey
     */
    public function removeColumnAtIndex($index)
    {
        unset($this->columns[$index]);

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

        $columnsAsArray = [];

        foreach ($this->columns as $column) {
            $columnsAsArray[] = $column->getName();
        }

        $arr = [
            'columns' => $columnsAsArray,
            'table' => $tableName,
        ];

        return $arr;
    }
}
