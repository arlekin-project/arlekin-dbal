<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\SqlBased;

/**
 * Represents a SQL-based result row.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ResultRow
{
    /**
     * The raw row data.
     *
     * @var array
     */
    protected $data;

    /**
     * Gets the raw row data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets the raw row data.
     *
     * @param array $data
     *
     * @return ResultRow
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Gets the datum for a given column name.
     *
     * @param string $columnName
     *
     * @return string
     */
    public function get($columnName)
    {
        $datum = $this->data[$columnName];

        return $datum;
    }
}
