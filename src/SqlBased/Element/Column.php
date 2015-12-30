<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\SqlBased\Element;

use Arlekin\Dbal\Exception\DbalException;

/**
 * Represents a SQL column.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
abstract class Column
{
    /**
     * The column's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The column's type.
     *
     * @var string
     */
    protected $type;

    /**
     * Whether the column is nullable.
     *
     * @var bool
     */
    protected $nullable;

    /**
     * Whether the column is autoincrementable.
     *
     * @var bool
     */
    protected $autoIncrement;

    /**
     * The column's parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * The table the column belongs to.
     *
     * @var Table
     */
    protected $table;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->autoIncrement = false;
        $this->parameters = [];
    }

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return Column
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type.
     *
     * @param ColumnType $type
     *
     * @return Column
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets whether the column is nullable.
     *
     * @return bool
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * Sets whether the column is nullable.
     *
     * @param bool $nullable
     *
     * @return Column
     */
    public function setNullable($nullable)
    {
        $this->nullable = (bool)$nullable;

        return $this;
    }

    /**
     * Gets whether the column is autoincrementable.
     *
     * @return bool
     */
    public function isAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * Sets whether the column is autoincrementable.
     *
     * @param bool $autoIncrement
     *
     * @return Column
     */
    public function setAutoIncrement($autoIncrement)
    {
        $this->autoIncrement = (bool)$autoIncrement;

        return $this;
    }

    /**
     * Gets the column's parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets the column's parameters.
     *
     * @param array $parameters
     *
     * @return Column
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param string $parameterName
     * @param mixed $value
     *
     * @return Column
     */
    public function setParameter($parameterName, $value)
    {
        $this->parameters[$parameterName] = $value;

        return $this;
    }

    /**
     * Gets the column's table.
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Sets the column's table.
     *
     * @param Table $table
     *
     * @return Column
     */
    public function setTable(Table $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Converts the table to an array.
     *
     * @todo Move the toArray responsability away from the Column
     *
     * @return array
     *
     * @throws DbalException if the column has no Table
     */
    public function toArray()
    {
        $table = $this->getTable();
        $columnName = $this->getName();

        if ($table === null) {
            throw new DbalException(
                sprintf(
                    'Missing table for column "%s".',
                    $columnName
                )
            );
        }

        $parameters = $this->getParameters();

        $arr = [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'nullable' => $this->isNullable(),
            'parameters' => $parameters,
            'autoIncrement' => $this->isAutoIncrement(),
            'table' => $table->getName(),
        ];

        return $arr;
    }
}