<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Element;

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
}
