<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\Exception\UnknownIndexClassException;
use Calam\Dbal\Driver\Pdo\MySql\Element\Exception\UnknownIndexTypeException;

/**
 * MySQL index.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class Index
{
    /**
     * Must be one of the values defined as a const in @see IndexClasses
     *
     * @var string
     */
    private $class;

    /**
     * Must be one of the values defined as a const in @see IndexTypes
     *
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

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
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class one of the values defined as a const in @see IndexClass
     *
     * @return Index
     *
     * @throws UnknownIndexClassException if given index class is not one of the values
     * defined as a const in @see IndexClasses
     */
    public function setClass(string $class): Index
    {
        if (!in_array($class, IndexClasses::$KNOWN)) {
            throw new UnknownIndexClassException($class);
        }

        $this->class = $class;

        return $this;
    }

    /**
     * @return string one of the values defined as a const in @see IndexClass
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type one of the values defined as a const in @see IndexType
     *
     * @return Index
     *
     * @throws UnknownIndexTypeException if given index type is not one of the values
     * defined as a const in @see IndexTypes
     */
    public function setType(string $type): Index
    {
        if (!in_array($type, IndexTypes::$KNOWN)) {
            throw new UnknownIndexTypeException($type);
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
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
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
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
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
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
