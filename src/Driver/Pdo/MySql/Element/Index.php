<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * Represents a MySQL index.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class Index
{
    /**
     * Index class.
     *
     * Must be one of the values defined as a const in @see IndexClass
     *
     * @var string
     */
    private $class;

    /**
     * Index type.
     *
     * @var string
     */
    private $type;

    /**
     * Index name.
     *
     * @var string
     */
    private $name;

    /**
     * Table the index belongs to.
     *
     * @var Table
     */
    private $table;

    /**
     * Index columns.
     *
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
     * Gets index class.
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Sets index class.
     *
     * @param string $class one of the values defined as a const in @see IndexClass
     *
     * @return Index
     *
     * @throws UnknownIndexClassException if given index class is not one of the values
     * defined as a const in @see IndexClass
     */
    public function setClass(string $class): Index
    {
        if (!in_array($class, IndexClass::$known)) {
            throw new UnknownIndexClassException($class);
        }

        $this->class = $class;

        return $this;
    }

    /**
     * Gets index type.
     *
     * @return string one of the values defined as a const in @see IndexClass
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets index type.
     *
     * @param string $type
     *
     * @return Index
     */
    public function setType(string $type): Index
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the index's name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the index's name.
     *
     * @param string $name
     *
     * @return Index
     */
    public function setName(string $name): Index
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the index's table.
     *
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * Sets the index's table.
     *
     * @param Table $table
     *
     * @return Index
     */
    public function setTable(Table $table): Index
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Gets the index's columns.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Sets the index's columns.
     *
     * @param array $columns
     *
     * @return Index
     */
    public function setColumns(array $columns): Index
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param Column $column
     *
     * @return Index
     */
    public function addColumn(Column $column): Index
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @param int $index
     *
     * @return Index
     */
    public function removeColumnAtIndex(int $index): Index
    {
        unset($this->columns[$index]);

        return $this;
    }

    /**
     * Converts the index into an array.
     *
     * @todo Move the toArray responsability away from the Index
     *
     * @return array
     */
    public function toArray(): array
    {
        $table = $this->getTable();
        $tableName = $table->getName();
        $indexName = $this->getName();

        $columnsAsArray = [];

        foreach ($this->columns as $column) {
            $columnsAsArray[] = $column->getName();
        }

        $arr = [
            'name' => $indexName,
            'kind' => $this->getType(),
            'columns' => $columnsAsArray,
            'table' => $tableName,
        ];

        return $arr;
    }
}
