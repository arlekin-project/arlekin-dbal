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
 * Represents a SQL foreign key.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
abstract class ForeignKey
{
    /**
     * The table the foreign key belongs to.
     *
     * @var Table
     */
    protected $table;

    /**
     * The columns that the foreign key uses from the table it belongs to.
     *
     * @var ArrayCollection
     */
    protected $columns;

    /**
     * The table the foreign key references.
     *
     * @var Table
     */
    protected $referencedTable;

    /**
     * The columns from the referenced table that the foreign key references.
     *
     * @var ArrayCollection
     */
    protected $referencedColumns;

    /**
     * The constraint to be applied on delete on the foreign key.
     *
     * @var string
     */
    protected $onDelete;

    /**
     * The constraint to be applied on update on the foreign key.
     *
     * @var string
     */
    protected $onUpdate;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->columns = new ArrayCollection();
        $this->referencedColumns = new ArrayCollection();
    }

    /**
     * Gets the table.
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Sets the table.
     *
     * @param Table $table
     *
     * @return ForeignKey
     */
    public function setTable(
        Table $table
    ) {
        $this->table = $table;

        return $this;
    }

    /**
     * Gets the columns.
     *
     * @return ArrayCollection
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Sets the columns.
     *
     * @param array|ArrayCollection $columns
     *
     * @return ForeignKey
     */
    public function setColumns(
        $columns
    ) {
        $this->columns
            ->replaceWithCollection(
                $columns
            );

        return $this;
    }

    /**
     * Adds a column to the foreign key columns.
     *
     * @param Column $column
     *
     * @return ForeignKey
     */
    public function addColumn(
        Column $column
    ) {
        $this->columns
            ->add(
                $column
            );

        return $this;
    }

    /**
     * Add columns to the foreign key columns.
     *
     * @param array|ArrayCollection $columns
     *
     * @return ForeignKey
     */
    public function addColumns(
        $columns
    ) {
        $this->columns
            ->mergeWithCollections(
                $columns
            );

        return $this;
    }

    /**
     * Gets the referenced table.
     *
     * @return Table
     */
    public function getReferencedTable()
    {
        return $this->referencedTable;
    }

    /**
     * Sets the referenced table.
     *
     * @param Table $referencedTable
     *
     * @return ForeignKey
     */
    public function setReferencedTable(
        Table $referencedTable
    ) {
        $this->referencedTable = $referencedTable;

        return $this;
    }

    /**
     * Gets the referenced columns.
     *
     * @return ArrayCollection
     */
    public function getReferencedColumns()
    {
        return $this->referencedColumns;
    }

    /**
     * Sets the referenced columns.
     *
     * @param array|ArrayCollection $referencedColumns
     *
     * @return ForeignKey
     */
    public function setReferencedColumns(
        $referencedColumns
    ) {
        $this->referencedColumns
            ->replaceWithCollection(
                $referencedColumns
            );

        return $this;
    }

    /**
     * Adds a referenced column to the foreign key referenced columns.
     *
     * @param Column $referencedColumn
     *
     * @return ForeignKey
     */
    public function addReferencedColumn(
        Column $referencedColumn
    ) {
        $this->referencedColumns
            ->add(
                $referencedColumn
            );

        return $this;
    }

    /**
     * Adds referenced columns to the foreign key referenced columns.
     *
     * @param array|ArrayCollection $referencedColumns
     *
     * @return ForeignKey
     */
    public function addReferencedColumns(
        $referencedColumns
    ) {
        $this->referencedColumns
            ->mergeWithCollections(
                $referencedColumns
            );

        return $this;
    }

    /**
     * Get the foreign key on delete constraint.
     *
     * @return string
     */
    public function getOnDelete()
    {
        return $this->onDelete;
    }

    /**
     * Sets the foreign key on delete constraint.
     *
     * @param string $onDelete
     *
     * @return ForeignKey
     */
    public function setOnDelete(
        $onDelete
    ) {
        $this->onDelete = $onDelete;

        return $this;
    }

    /**
     * Gets the foreign key on update constraint.
     *
     * @return string
     */
    public function getOnUpdate()
    {
        return $this->onUpdate;
    }

    /**
     * Sets the foreign key on update constraint.
     *
     * @param string $onUpdate
     *
     * @return ForeignKey
     */
    public function setOnUpdate(
        $onUpdate
    ) {
        $this->onUpdate = $onUpdate;

        return $this;
    }

    /**
     * Converts the foreign key to array.
     *
     * @todo Move the toArray responsability away from the ForeignKey
     *
     * @return array
     */
    public function toArray()
    {
        $table = $this->getTable();
        $tableName = $table->getName();
        $referencedTable = $this->getReferencedTable();
        $referencedTableName = $referencedTable->getName();

        $columnsAsArray = array();

        foreach ($this->columns as $column) {
            $columnsAsArray[] = $column->getName();
        }

        $referencedColumnsAsArray = array();

        foreach ($this->referencedColumns as $column) {
            $referencedColumnsAsArray[] = $column->getName();
        }

        $arr = array(
            'table' => $tableName,
            'columns' => $columnsAsArray,
            'referencedTable' => $referencedTableName,
            'referencedColumns' => $referencedColumnsAsArray,
            'onDelete' => $this->getOnDelete(),
            'onUpdate' => $this->getOnUpdate()
        );

        return $arr;
    }
}
