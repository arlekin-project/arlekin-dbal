<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Element;

use Arlekin\Dbal\Driver\Pdo\MySql\Exception\PdoMySqlDriverException;
use Arlekin\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper;
use Arlekin\Dbal\Exception\DbalException;

/**
 * Represents a MySQL table.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class Table
{
    /**
     * The table's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The table's primary key.
     *
     * @var PrimaryKey|null
     */
    protected $primaryKey;

    /**
     * The table's columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * The table's foreign keys.
     *
     * @var array
     */
    protected $foreignKeys;

    /**
     * The table's indexes.
     *
     * @var array
     */
    protected $indexes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->columns = [];
        $this->foreignKeys = [];
        $this->primaryKey = null;
        $this->indexes = [];
    }

    /**
     * Gets the table's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the table's name.
     *
     * @param string $name
     *
     * @return Table
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the table's primary key.
     *
     * @return PrimaryKey|null
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Sets the table's primary key.
     *
     * @param PrimaryKey $primaryKey
     *
     * @return Table
     */
    public function setPrimaryKey(PrimaryKey $primaryKey = null)
    {
        if ($primaryKey === null) {
            $this->primaryKey->setTable(null);
        } else {
            $primaryKey->setTable($this);
        }

        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * Gets the table's columns.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Sets the table's columns.
     *
     * @param array $columns
     *
     * @return Table
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        foreach ($this->columns as $column) {
            $column->setTable($this);
        }

        return $this;
    }

    /**
     * @param Column $column
     *
     * @return Table
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;

        $column->setTable($this);

        return $this;
    }

    /**
     * @param int $index
     *
     * @return Table
     */
    public function removeColumnAtIndex($index)
    {
        unset($this->columns[$index]);

        return $this;
    }

    /**
     * Gets the table's foreign keys.
     *
     * @return array
     */
    public function getForeignKeys()
    {
        return $this->foreignKeys;
    }

    /**
     * Sets the table's foreign keys.
     *
     * @param array $foreignKeys
     *
     * @return Table
     */
    public function setForeignKeys(array $foreignKeys)
    {
        $this->foreignKeys = $foreignKeys;

        foreach ($this->foreignKeys as $foreignKey) {
            $foreignKey->setTable($this);
        }

        return $this;
    }

    /**
     * @param ForeignKey $foreignKey
     *
     * @return Table
     */
    public function addForeignKey(ForeignKey $foreignKey)
    {
        $this->foreignKeys[] = $foreignKey;

        $foreignKey->setTable($this);

        return $this;
    }

    /**
     * @param int $index
     *
     * @return Table
     */
    public function removeForeignKeyAtIndex($index)
    {
        unset($this->foreignKeys[$index]);

        return $this;
    }

    /**
     * Gets the table's indexes.
     *
     * @return array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Sets the table's indexes.
     *
     * @param array $indexes
     *
     * @return Table
     */
    public function setIndexes(array $indexes)
    {
        $this->indexes = $indexes;

        foreach ($this->indexes as $index) {
            $index->setTable($this);
        }

        return $this;
    }

    /**
     * @param Index $index
     *
     * @return Table
     */
    public function addIndex(Index $index)
    {
        $this->indexes[] = $index;

        $index->setTable($this);

        return $this;
    }

    /**
     * @param int $index
     *
     * @return Table
     */
    public function removeIndexAtIndex($index)
    {
        unset($this->indexes[$index]);

        return $this;
    }

    /**
     * Converts a table into an array.
     *
     * @todo Move the toArray responsability away from the table.
     *
     * @return array
     */
    public function toArray()
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
     * @return boolean
     */
    public function hasForeignKeyWithColumnsAndReferencedColumnsNamed(
        array $columnsNames,
        $referencedTableName,
        array $referencedColumnsNames
    ) {
        $foreignKeyAsArray = [
            'table' => $this->getName(),
            'columns' => $columnsNames,
            'referencedTable' => $referencedTableName,
            'referencedColumns' => $referencedColumnsNames,
        ];

        $foreignKeyToRemoveHash = MySqlHelper::getForeignKeyUniqueNameFromForeignKeyAsArray($foreignKeyAsArray);

        $has = false;
        $foreignKeys = $this->getForeignKeys();

        foreach ($foreignKeys as $tableForeignKey) {
            $tableForeignKeyHash = MySqlHelper::getForeignKeyUniqueNameFromForeignKey($tableForeignKey);

            if ($tableForeignKeyHash === $foreignKeyToRemoveHash) {
                $has = true;
            }
        }

        return $has;
    }

    /**
     * Whether the table has the given column.
     *
     * @param Column $column
     *
     * @return bool
     */
    public function hasColumn(Column $column)
    {
        return in_array(
            $column,
            $this->getColumns()
        );
    }

    /**
     * Whether the table has a column with given name.
     *
     * @param string $columnName
     *
     * @return bool
     */
    public function hasColumnWithName($columnName)
    {
        foreach ($this->getColumns() as $column) {
            if ($column->getName() === $columnName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the table has an index with given name.
     *
     * @param string $indexName
     *
     * @return bool
     */
    public function hasIndexWithName($indexName)
    {
        foreach ($this->getIndexes() as $index) {
            if ($index->getName() === $indexName) {
                return true;
            }
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
    public function hasPrimaryKeyWithColumnsNamed(array $columnNames)
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
     * Gets an index with given name.
     *
     * @param string $indexName
     *
     * @return Index
     *
     * @throws DbalException if no index with given name is found
     */
    public function getIndexWithName($indexName)
    {
        foreach ($this->getIndexes() as $index) {
            if ($index->getName() === $indexName) {
                return $index;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Table "%s" has no index with name "%s".',
                $this->getName(),
                $indexName
            )
        );
    }

    /**
     * Gets a column with given name from given table.
     *
     * @param string $columnName
     *
     * @return Column
     *
     * @throws DbalException
     */
    public function getColumnWithName($columnName)
    {
        foreach ($this->getColumns() as $column) {
            if ($column->getName() === $columnName) {
                return $column;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Table "%s" has no column with name "%s".',
                $this->getName(),
                $columnName
            )
        );
    }
}
