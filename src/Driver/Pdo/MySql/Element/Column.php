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
     * @param Table $table
     * @param string $name
     * @param string $dataType
     * @param bool $nullable
     * @param bool $autoIncrementable
     * @param array $parameters
     */
    public function __construct(
        Table $table,
        string $name,
        string $dataType,
        bool $nullable,
        bool $autoIncrementable = false,
        array $parameters = []
    ) {
        $this->table = $table;
        $this->name = $name;
        $this->dataType = $dataType;
        $this->nullable = $nullable;
        $this->autoIncrementable = $autoIncrementable;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @return bool
     */
    public function isAutoIncrementable(): bool
    {
        return $this->autoIncrementable;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
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
            'autoIncrementable' => $this->isAutoIncrementable(),
            'parameters' => $parameters,
            'table' => $table->getName(),
        ];

        return $arr;
    }
}
