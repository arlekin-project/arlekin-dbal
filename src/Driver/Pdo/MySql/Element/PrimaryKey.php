<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * MySQL primary key.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class PrimaryKey
{
    /**
     * @var Table
     */
    private $table;

    /**
     * @var Column[]
     */
    private $columns;

    /**
     * @param Table $table
     * @param Column[] $columns
     */
    public function __construct(Table $table, array $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
