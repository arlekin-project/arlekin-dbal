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
     * @var Table
     */
    private $table;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param Table $table
     * @param string $name
     * @param array $parameters
     */
    public function __construct(Table $table, string $name, array $parameters = [])
    {
        $this->table = $table;
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
