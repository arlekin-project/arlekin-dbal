<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\SqlBased\Element;

use Arlecchino\Core\Collection\ArrayCollection;

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
     * @var ArrayCollection
     */
    protected $columns;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->columns = new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Sets the primary key's columns.
     *
     * @param array|ArrayCollection $columns
     *
     * @return PrimaryKey
     */
    public function setColumns(
        $columns
    ) {
        $this
            ->columns
            ->replaceWithCollection(
                $columns
            );

        return $this;
    }

    /**
     * Adds a column to the primary key's columns.
     *
     * @param Column $column
     * @return PrimaryKey
     */
    public function addColumn(
        Column $column
    ) {
        $this
            ->columns
            ->add(
                $column
            );

        return $this;
    }

    /**
     * Adds columns to the primary key's columns.
     *
     * @param array|ArrayCollection $columns
     *
     * @return PrimaryKey
     */
    public function addColumns(
        $columns
    ) {
        $this
            ->columns
            ->mergeWithCollections(
                $columns
            );

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
