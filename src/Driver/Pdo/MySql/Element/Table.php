<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\Exception\MissingTableColumnException;
use Calam\Dbal\Driver\Pdo\MySql\Element\Exception\MissingTableIndexException;
use Calam\Dbal\Driver\Pdo\MySql\Util\Util;

/**
 * MySQL table.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class Table
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Column[]
     */
    private $columns;

    /**
     * @var ForeignKey[]
     */
    private $foreignKeys;

    /**
     * @var PrimaryKey|null
     */
    private $primaryKey;

    /**
     * @var Index[]
     */
    private $indexes;

    /**
     * @param string $name
     * @param Column[] $columns
     * @param ForeignKey[] $foreignKeys
     * @param Index[] $indexes
     * @param PrimaryKey|null $primaryKey
     */
    public function __construct(
        string $name,
        array $columns,
        array $foreignKeys = [],
        array $indexes = [],
        PrimaryKey $primaryKey = null
    ) {
        $this->name = $name;
        $this->columns = $columns;
        $this->foreignKeys = $foreignKeys;
        $this->indexes = $indexes;
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PrimaryKey|null
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getForeignKeys(): array
    {
        return $this->foreignKeys;
    }

    /**
     * @return array
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @todo Move the toArray responsibility away from the table.
     *
     * @return array
     */
    public function toArray(): array
    {
        $columnsAsArray = [];
        $indexesAsArray = [];
        $foreignKeysAsArray = [];

        foreach ($this->columns as $column) {
            $columnAsArray = $column->toArray();
            unset($columnAsArray['table']);
            $columnsAsArray[] = $columnAsArray;
        }

        foreach ($this->indexes as $index) {
            $indexAsArray = $index->toArray();
            unset($indexAsArray['table']);
            $indexesAsArray[] = $indexAsArray;
        }

        if (isset($this->primaryKey)) {
            $primaryKeyAsArray = $this->primaryKey->toArray();
        } else {
            $primaryKeyAsArray = null;
        }

        unset($primaryKeyAsArray['table']);

        foreach ($this->foreignKeys as $foreignKey) {
            $foreignKeyAsArray = $foreignKey->toArray();
            unset($foreignKeyAsArray['table']);
            $foreignKeysAsArray[] = $foreignKeyAsArray;
        }

        $arr = [
            'name' => $this->getName(),
            'columns' => $columnsAsArray,
            'primaryKey' => $primaryKeyAsArray,
            'indexes' => $indexesAsArray,
            'foreignKeys' => $foreignKeysAsArray,
        ];

        return $arr;
    }

    /**
     * Whether a foreign key with given column names,
     * referenced table name and referenced columns name as identifiers
     * exists.
     *
     * @param array $columnsNames
     * @param string $referencedTableName
     * @param array $referencedColumnsNames
     *
     * @return bool
     */
    public function hasForeignKeyWithColumnsAndReferencedColumnsNamed(
        array $columnsNames,
        string $referencedTableName,
        array $referencedColumnsNames
    ): bool {
        $foreignKeyAsArray = [
            'table' => $this->getName(),
            'columns' => $columnsNames,
            'referencedTable' => $referencedTableName,
            'referencedColumns' => $referencedColumnsNames,
        ];

        $foreignKeyToRemoveHash = Util::getForeignKeyUniqueNameFromForeignKeyAsArray($foreignKeyAsArray);

        $has = false;
        $foreignKeys = $this->getForeignKeys();

        foreach ($foreignKeys as $tableForeignKey) {
            $tableForeignKeyHash = Util::getForeignKeyUniqueNameFromForeignKey($tableForeignKey);

            if ($tableForeignKeyHash === $foreignKeyToRemoveHash) {
                $has = true;
            }
        }

        return $has;
    }

    /**
     * @param Column $column
     *
     * @return bool
     */
    public function hasColumn(Column $column): bool
    {
        return in_array(
            $column,
            $this->getColumns()
        );
    }

    /**
     * @param string $columnName
     *
     * @return bool
     */
    public function hasColumnWithName(string $columnName): bool
    {
        foreach ($this->getColumns() as $column) {
            if ($column->getName() === $columnName) {
                return true;
            }

            unset($column);
        }

        return false;
    }

    /**
     * @param string $indexName
     *
     * @return bool
     */
    public function hasIndexWithName(string $indexName): bool
    {
        foreach ($this->getIndexes() as $index) {
            if ($index->getName() === $indexName) {
                return true;
            }

            unset($index);
        }

        return false;
    }

    /**
     * Whether the table has a primary key
     * which columns names are identical to the given column names.
     *
     * @param array $columnNames
     *
     * @return bool
     */
    public function hasPrimaryKeyWithColumnsNamed(array $columnNames): bool
    {
        $primaryKey = $this->getPrimaryKey();

        if ($primaryKey === null) {
            $hasPrimaryKeyWithColumnsNamed = false;
        } else {
            $primaryKeyAsArray = $primaryKey->toArray();

            $hasPrimaryKeyWithColumnsNamed = array_values(
                $primaryKeyAsArray['columns']
            ) === array_values(
                $columnNames
            );
        }

        return $hasPrimaryKeyWithColumnsNamed;
    }

    /**
     * @param string $indexName
     *
     * @return Index
     *
     * @throws MissingTableIndexException if no index with given name is found
     */
    public function getIndexWithName(string $indexName): Index
    {
        foreach ($this->getIndexes() as $index) {
            if ($index->getName() === $indexName) {
                return $index;
            }

            unset($index);
        }

        throw new MissingTableIndexException($this->getName(), $indexName);
    }

    /**
     * @param string $columnName
     *
     * @return Column
     *
     * @throws MissingTableColumnException if no column with given name is found
     */
    public function getColumnWithName(string $columnName): Column
    {
        foreach ($this->getColumns() as $column) {
            if ($column->getName() === $columnName) {
                return $column;
            }

            unset($column);
        }

        throw new MissingTableColumnException($this->getName(), $columnName);
    }
}
