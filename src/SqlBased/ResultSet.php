<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\SqlBased;

/**
 * Represents a set of results returned by a a SQL-based query.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ResultSet
{
    /**
     * The returned rows.
     *
     * @var array
     */
    protected $rows;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->rows = [];
    }

    /**
     * Gets the returned rows.
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Sets the returned rows.
     *
     * @param array $rows
     *
     * @return ResultSet
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;

        return $this;
    }
}
