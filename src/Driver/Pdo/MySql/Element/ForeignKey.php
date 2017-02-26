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
     * @param Table $table
     * @param array $columns
     * @param Table $referencedTable
     * @param array $referencedColumns
     * @param string $onDelete
     * @param string $onUpdate
     */
    public function __construct(
        Table $table,
        array $columns,
        Table $referencedTable,
        array $referencedColumns,
        string $onDelete = ForeignKeyOnDeleteReferenceOptions::ON_DELETE_RESTRICT,
        string $onUpdate = ForeignKeyOnUpdateReferenceOptions::ON_UPDATE_RESTRICT
    ) {
        $this->table = $table;
        $this->columns = $columns;
        $this->referencedTable = $referencedTable;
        $this->referencedColumns = $referencedColumns;
        $this->onDelete = $onDelete;
        $this->onUpdate = $onUpdate;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return Table
     */
    public function getReferencedTable(): Table
    {
        return $this->referencedTable;
    }

    /**
     * @return array
     */
    public function getReferencedColumns(): array
    {
        return $this->referencedColumns;
    }

    /**
     * @return string
     */
    public function getOnDelete(): string
    {
        return $this->onDelete;
    }

    /**
     * @return string
     */
    public function getOnUpdate(): string
    {
        return $this->onUpdate;
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
