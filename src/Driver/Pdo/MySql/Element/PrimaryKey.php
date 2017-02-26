<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * MySQL primary key.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class PrimaryKey
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
     * Constructor.
     */
    public function __construct()
    {
        $this->columns = [];
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
     * @return PrimaryKey
     */
    public function setTable(Table $table = null): PrimaryKey
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
     * @return PrimaryKey
     */
    public function setColumns(array $columns): PrimaryKey
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param Column $column
     *
     * @return PrimaryKey
     */
    public function addColumn(Column $column): PrimaryKey
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * TODO See if it can be removed?
     *
     * @param int $index
     *
     * @return PrimaryKey
     */
    public function removeColumnAtIndex($index): PrimaryKey
    {
        unset($this->columns[$index]);

        return $this;
    }

    /**
     * @todo Move the toArray responsibility away from the primary key.
     *
     * @return array
     */
    public function toArray(): array
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
