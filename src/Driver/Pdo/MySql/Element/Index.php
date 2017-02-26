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
     * @var string
     */
    private $name;

    /**
     * @var Table
     */
    private $table;

    /**
     * @var Column[]
     */
    private $columns;

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
     * @param Table $table
     * @param string $name
     * @param Column[] $columns
     * @param string $class  one of the values defined as a const in @see IndexClass
     * @param string $type   one of the values defined as a const in @see IndexType
     *
     * @throws UnknownIndexClassException if given index class is not one of the values
     * defined as a const in @see IndexClasses
     * @throws UnknownIndexTypeException  if given index type is not one of the values
     * defined as a const in @see IndexTypes
     */
    public function __construct(
        Table $table,
        string $name,
        array $columns,
        string $class,
        string $type
    ) {
        if (!in_array($class, IndexClasses::$KNOWN)) {
            throw new UnknownIndexClassException($class);
        }

        if (!in_array($type, IndexTypes::$KNOWN)) {
            throw new UnknownIndexTypeException($type);
        }

        $this->table = $table;
        $this->name = $name;
        $this->columns = $columns;
        $this->class = $class;
        $this->type = $type;
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
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return string one of the values defined as a const in @see IndexClass
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string one of the values defined as a const in @see IndexType
     */
    public function getType(): string
    {
        return $this->type;
    }
}
