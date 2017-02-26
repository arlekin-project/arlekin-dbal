<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

use Calam\Dbal\Exception\DbalException;

/**
 * MySQL column.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class Column
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $dataType;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @var bool
     */
    private $autoIncrementable;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var Table
     */
    private $table;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->autoIncrementable = false;
        $this->parameters = [];
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
     * @return Column
     */
    public function setName(string $name): Column
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * @param string $dataType
     *
     * @return Column
     */
    public function setDataType(string $dataType): Column
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @param bool $nullable
     *
     * @return Column
     */
    public function setNullable(bool $nullable): Column
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoIncrementable(): bool
    {
        return $this->autoIncrementable;
    }

    /**
     * @param bool $autoIncrementable
     *
     * @return Column
     */
    public function setAutoIncrementable(bool $autoIncrementable): Column
    {
        $this->autoIncrementable = $autoIncrementable;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return Column
     */
    public function setParameters(array $parameters): Column
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
    public function setParameter(string $parameterName, $value): Column
    {
        $this->parameters[$parameterName] = $value;

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
     * @return Column
     */
    public function setTable(Table $table): Column
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
    public function toArray(): array
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
            'dataType' => $this->getDataType(),
            'nullable' => $this->isNullable(),
            'parameters' => $parameters,
            'autoIncrementable' => $this->isAutoIncrementable(),
            'table' => $table->getName(),
        ];

        return $arr;
    }
}
