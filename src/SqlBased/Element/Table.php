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
 * Represents a SQL table.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
abstract class Table
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
     * @var ArrayCollection
     */
    protected $columns;

    /**
     * The table's foreign keys.
     *
     * @var ArrayCollection
     */
    protected $foreignKeys;

    /**
     * The table's indexes.
     *
     * @var ArrayCollection
     */
    protected $indexes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->columns = new ArrayCollection();
        $this->foreignKeys = new ArrayCollection();
        $this->primaryKey = null;
        $this->indexes = new ArrayCollection();
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
    public function setName(
        $name
    ) {
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
    public function setPrimaryKey(
        PrimaryKey $primaryKey = null
    ) {
        if ($primaryKey === null) {
            $this
                ->primaryKey
                ->setTable(null);
        } else {
            $primaryKey
                ->setTable($this);
        }
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * Gets the table's columns.
     *
     * @return ArrayCollection
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Sets the table's columns.
     *
     * @param array|ArrayCollection $columns
     *
     * @return Table
     */
    public function setColumns(
        $columns
    ) {
        $this
            ->columns
            ->replaceWithCollection(
                $columns
            );
        foreach ($this->columns as $column) {
            $column
                ->setTable(
                    $this
                );
        }

        return $this;
    }

    /**
     * Adds a column to the table's columns.
     *
     * @param Column $column
     *
     * @return Table
     */
    public function addColumn(
        Column $column
    ) {
        $column
            ->setTable(
                $this
            );
        $this
            ->columns
            ->add(
                $column
            );

        return $this;
    }

    /**
     * Adds columns to the table's columns.
     *
     * @param array|ArrayCollection $columns
     *
     * @return Table
     */
    public function addColumns(
        $columns
    ) {
        foreach ($columns as $column) {
            /* @var $column Column */
            $column->setTable($this);
        }
        $this
            ->columns
            ->mergeWithCollections(
                $columns
            );

        return $this;
    }

    /**
     * Gets the table's foreign keys.
     *
     * @return ArrayCollection
     */
    public function getForeignKeys()
    {
        return $this->foreignKeys;
    }

    /**
     * Sets the table's foreign keys.
     *
     * @param string $foreignKeys
     *
     * @return Table
     */
    public function setForeignKeys(
        $foreignKeys
    ) {
        $this
            ->foreignKeys
            ->replaceWithCollection(
                $foreignKeys
            );
        foreach ($this->foreignKeys as $foreignKey) {
            $foreignKey->setTable(
                $this
            );
        }

        return $this;
    }

    /**
     * Adds a foreign key to the table's foreign keys.
     *
     * @param ForeignKey $foreignKey
     *
     * @return Table
     */
    public function addForeignKey(
        ForeignKey $foreignKey
    ) {
        $foreignKey
            ->setTable(
                $this
            );
        $this
            ->foreignKeys
            ->add(
                $foreignKey
            );

        return $this;
    }

    /**
     * Adds foreign keys to the table's foreign keys.
     *
     * @param array|ArrayCollection $foreignKeys
     *
     * @return Table
     */
    public function addForeignKeys(
        $foreignKeys
    ) {
        foreach ($foreignKeys as $foreignKey) {
            /* @var $foreignKey ForeignKey */
            $foreignKey
                ->setTable(
                    $this
                );
        }
        $this
            ->foreignKeys
            ->mergeWithCollections(
                $foreignKeys
            );

        return $this;
    }

    /**
     * Gets the table's indexes.
     *
     * @return ArrayCollection
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Sets the table's indexes.
     *
     * @param array|ArrayCollection $indexes
     *
     * @return Table
     */
    public function setIndexes(
        $indexes
    ) {
        $this
            ->indexes
            ->replaceWithCollection(
                $indexes
            );
        foreach ($this->indexes as $index) {
            $index
                ->setTable(
                    $this
                );
        }

        return $this;
    }

    /**
     * Adds an index to the table's indexes.
     *
     * @param Index $index
     *
     * @return Table
     */
    public function addIndex(
        Index $index
    ) {
        $index
            ->setTable(
                $this
            );
        $this
            ->indexes
            ->add(
                $index
            );

        return $this;
    }

    /**
     * Adds indexes to the table's indexes.
     *
     * @param array|ArrayCollection $indexes
     *
     * @return Table
     */
    public function addIndexes(
        $indexes
    ) {
        $this
            ->indexes
            ->mergeWithCollections(
                $indexes
            );
        foreach ($this->indexes as $index) {
            $index->setTable(
                $this
            );
        }

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
        $columnsAsArray = array();
        $indexesAsArray = array();
        $foreignKeysAsArray = array();

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
            $primaryKeyAsArray = $this->primaryKey
                ->toArray();
        } else {
            $primaryKeyAsArray = null;
        }

        unset($primaryKeyAsArray['table']);

        foreach ($this->foreignKeys as $foreignKey) {
            $foreignKeyAsArray = $foreignKey->toArray();
            unset($foreignKeyAsArray['table']);
            $foreignKeysAsArray[] = $foreignKeyAsArray;
        }

        $arr = array(
            'name' => $this->getName(),
            'columns' => $columnsAsArray,
            'primaryKey' => $primaryKeyAsArray,
            'indexes' => $indexesAsArray,
            'foreignKeys' => $foreignKeysAsArray
        );

        return $arr;
    }
}
