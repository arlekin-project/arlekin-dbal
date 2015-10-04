<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\SqlBased\Element;

/**
 * Represents a SQL index.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
abstract class Index
{
    /**
     * The index's kind.
     *
     * @var string
     */
    protected $kind;

    /**
     * The index's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The table the index belongs to.
     *
     * @var Table
     */
    protected $table;

    /**
     * The index's columns.
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
     * Gets the index's kind.
     *
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * Sets the index's kind.
     *
     * @param string $kind
     *
     * @return Index
     */
    public function setKind($kind)
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * Gets the index's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the index's name.
     *
     * @param string $name
     *
     * @return Index
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the index's table.
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Sets the index's table.
     *
     * @param Table $table
     *
     * @return Index
     */
    public function setTable(Table $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Gets the index's columns.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Sets the index's columns.
     *
     * @param array $columns
     *
     * @return Index
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param Column $column
     *
     * @return Index
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Converts the index into an array.
     *
     * @todo Move the toArray responsability away from the Index
     *
     * @return array
     */
    public function toArray()
    {
        $table = $this->getTable();
        $tableName = $table->getName();
        $indexName = $this->getName();

        $columnsAsArray = [];

        foreach ($this->columns as $column) {
            $columnsAsArray[] = $column->getName();
        }

        $arr = [
            'name' => $indexName,
            'kind' => $this->getKind(),
            'columns' => $columnsAsArray,
            'table' => $tableName,
        ];

        return $arr;
    }
}
