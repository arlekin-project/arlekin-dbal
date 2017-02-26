<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * MySQL foreign key.
 *
 * @see https://dev.mysql.com/doc/refman/5.5/en/create-table-foreign-keys.html
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class ForeignKey
{
    /**
     * @var Table
     */
    private $table;

    /**
     * @var array
     */
    private $columns;

    /**
     * @var Table
     */
    private $referencedTable;

    /**
     * @var array
     */
    private $referencedColumns;

    /**
     * @var string
     */
    private $onDelete;

    /**
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

        $this->onDelete = ForeignKeyOnDeleteReferenceOptions::ON_DELETE_RESTRICT;
        $this->onUpdate = ForeignKeyOnUpdateReferenceOptions::ON_UPDATE_RESTRICT;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @param Table $table
     *
     * @return ForeignKey
     */
    public function setTable(Table $table): ForeignKey
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     *
     * @return ForeignKey
     */
    public function setColumns(array $columns): ForeignKey
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param Column $column
     *
     * @return ForeignKey
     */
    public function addColumn(Column $column): ForeignKey
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * TODO See if it can be removed?
     *
     * @param int $index
     *
     * @return ForeignKey
     */
    public function removeColumnAtIndex(int $index): ForeignKey
    {
        unset($this->columns[$index]);

        return $this;
    }

    /**
     * @return Table
     */
    public function getReferencedTable(): Table
    {
        return $this->referencedTable;
    }

    /**
     * @param Table $referencedTable
     *
     * @return ForeignKey
     */
    public function setReferencedTable(Table $referencedTable): ForeignKey
    {
        $this->referencedTable = $referencedTable;

        return $this;
    }

    /**
     * @return array
     */
    public function getReferencedColumns(): array
    {
        return $this->referencedColumns;
    }

    /**
     * @param array $referencedColumns
     *
     * @return ForeignKey
     */
    public function setReferencedColumns(array $referencedColumns): ForeignKey
    {
        $this->referencedColumns = $referencedColumns;

        return $this;
    }

    /**
     * @param Column $referencedColumn
     *
     * @return ForeignKey
     */
    public function addReferencedColumn(Column $referencedColumn): ForeignKey
    {
        $this->referencedColumns[] = $referencedColumn;

        return $this;
    }

    /**
     * TODO See if it can be removed?
     *
     * @param int $index
     *
     * @return ForeignKey
     */
    public function removeReferencedColumnAtIndex(int $index): ForeignKey
    {
        unset($this->referencedColumns[$index]);

        return $this;
    }

    /**
     * @return string
     */
    public function getOnDelete(): string
    {
        return $this->onDelete;
    }

    /**
     * @param string $onDelete
     *
     * @return ForeignKey
     */
    public function setOnDelete(string $onDelete): ForeignKey
    {
        $this->onDelete = $onDelete;

        return $this;
    }

    /**
     * @return string
     */
    public function getOnUpdate(): string
    {
        return $this->onUpdate;
    }

    /**
     * @param string $onUpdate
     *
     * @return ForeignKey
     */
    public function setOnUpdate(string $onUpdate): ForeignKey
    {
        $this->onUpdate = $onUpdate;

        return $this;
    }

    /**
     * Converts the foreign key to array.
     *
     * @todo Move the toArray responsibility away from the ForeignKey
     *
     * @return array
     */
    public function toArray(): array
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
