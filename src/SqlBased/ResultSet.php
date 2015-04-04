<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\SqlBased;

use Arlecchino\Core\Collection\ArrayCollection;

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
     * @var ArrayCollection
     */
    protected $rows;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->rows = new ArrayCollection();
    }

    /**
     * Gets the returned rows.
     *
     * @return ArrayCollection
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Sets the returned rows.
     *
     * @param array|ArrayCollection $rows
     *
     * @return ResultSet
     */
    public function setRows(
        $rows
    ) {
        $this->rows
            ->replaceWithCollection(
                $rows
            );

        return $this;
    }
}
