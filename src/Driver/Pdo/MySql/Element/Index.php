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
     * @param Table $table
     * @param string $class  one of the values defined as a const in @see IndexClass
     * @param string $type   one of the values defined as a const in @see IndexType
     * @param string $name
     * @param array $columns
     *
     * @throws UnknownIndexClassException if given index class is not one of the values
     * defined as a const in @see IndexClasses
     * @throws UnknownIndexTypeException  if given index type is not one of the values
     * defined as a const in @see IndexTypes
     */
    public function __construct(
        Table $table,
        string $class,
        string $type,
        string $name,
        array $columns
    ) {
        if (!in_array($class, IndexClasses::$KNOWN)) {
            throw new UnknownIndexClassException($class);
        }

        if (!in_array($type, IndexTypes::$KNOWN)) {
            throw new UnknownIndexTypeException($type);
        }

        $this->table = $table;
        $this->class = $class;
        $this->type = $type;
        $this->name = $name;
        $this->columns = $columns;
    }

    /**
     * @return string one of the values defined as a const in @see IndexClass
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
