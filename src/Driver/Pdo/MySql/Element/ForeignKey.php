<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * Represents a MySQL foreign key.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ForeignKey
{
    /**
     * The table the foreign key belongs to.
     *
     * @var Table
     */
    private $table;

    /**
     * The columns that the foreign key uses from the table it belongs to.
     *
     * @var array
     */
    private $columns;

    /**
     * The table the foreign key references.
     *
     * @var Table
     */
    private $referencedTable;

    /**
     * The columns from the referenced table that the foreign key references.
     *
     * @var array
     */
    private $referencedColumns;

    /**
     * The constraint to be applied on delete on the foreign key.
     *
     * @var string
     */
    private $onDelete;

    /**
     * The constraint to be applied on update on the foreign key.
     *
     * @var string
     */
    private $onUpdate;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->columns = [];
        $this->referencedColumns = [];

        $this->onDelete = ForeignKeyOnDeleteConstraint::ON_DELETE_RESTRICT;
        $this->onUpdate = ForeignKeyOnUpdateConstraint::ON_UPDATE_RESTRICT;
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
    public function setTable(Table $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Gets the columns.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Sets the columns.
     *
     * @param array $columns
     *
     * @return ForeignKey
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Adds a column to the foreign key columns.
     *
     * @param Column $column
     *
     * @return ForeignKey
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Removes at column at given index.
     *
     * @param int $index
     *
     * @return ForeignKey
     */
    public function removeColumnAtIndex($index)
    {
        unset($this->columns[$index]);

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
    public function setReferencedTable(Table $referencedTable)
    {
        $this->referencedTable = $referencedTable;

        return $this;
    }

    /**
     * Gets the referenced columns.
     *
     * @return array
     */
    public function getReferencedColumns()
    {
        return $this->referencedColumns;
    }

    /**
     * Sets the referenced columns.
     *
     * @param array $referencedColumns
     *
     * @return ForeignKey
     */
    public function setReferencedColumns(array $referencedColumns)
    {
        $this->referencedColumns = $referencedColumns;

        return $this;
    }

    public function addReferencedColumn(Column $referencedColumn)
    {
        $this->referencedColumns[] = $referencedColumn;

        return $this;
    }

    public function removeReferencedColumnAtIndex($index)
    {
        unset($this->referencedColumns[$index]);

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
    public function setOnDelete($onDelete)
    {
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
    public function setOnUpdate($onUpdate)
    {
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

        $columnsAsArray = [];

        foreach ($this->columns as $column) {
            $columnsAsArray[] = $column->getName();
        }

        $referencedColumnsAsArray = [];

        foreach ($this->referencedColumns as $column) {
            $referencedColumnsAsArray[] = $column->getName();
        }

        $arr = [
            'table' => $tableName,
            'columns' => $columnsAsArray,
            'referencedTable' => $referencedTableName,
            'referencedColumns' => $referencedColumnsAsArray,
            'onDelete' => $this->getOnDelete(),
            'onUpdate' => $this->getOnUpdate(),
        ];

        return $arr;
    }
}
