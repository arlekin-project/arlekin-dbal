<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

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
     * @param string $name
     * @param string $dataType
     * @param bool $nullable
     * @param bool $autoIncrementable
     * @param array $parameters
     */
    public function __construct(
        string $name,
        string $dataType,
        bool $nullable,
        bool $autoIncrementable = false,
        array $parameters = []
    ) {
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
     * @todo Move the toArray responsability away from the Column
     *
     * @return array
     */
    public function toArray(): array
    {
        $parameters = $this->getParameters();

        $arr = [
            'name' => $this->getName(),
            'dataType' => $this->getDataType(),
            'nullable' => $this->isNullable(),
            'autoIncrementable' => $this->isAutoIncrementable(),
            'parameters' => $parameters,
        ];

        return $arr;
    }
}
