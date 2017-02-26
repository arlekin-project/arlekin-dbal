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
     * @param Table $table
     * @param array $columns
     */
    public function __construct(Table $table, array $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
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
