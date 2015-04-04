<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\SqlBased;

use Arlekin\Core\Collection\ArrayCollection;

/**
 * Represents a SQL-based query.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class Query
{
    /**
     * The SQL query as string.
     *
     * @var string
     */
    protected $sql;

    /**
     * The query parameters.
     *
     * @var ArrayCollection
     */
    protected $parameters;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->parameters = new ArrayCollection();
    }

    /**
     * Gets the SQL query as string.
     *
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Sets the SQL query as string.
     *
     * @param string $sql
     *
     * @return Query
     */
    public function setSql(
        $sql
    ) {
        $this->sql = $sql;

        return $this;
    }

    /**
     * Gets the query parameters.
     *
     * @return ArrayCollection
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets the query parameters.
     *
     * @param array $parameters
     *
     * @return Query
     */
    public function setParameters(
        $parameters
    ) {
        $this
            ->parameters
            ->replaceWithCollection(
                $parameters
            );

        return $this;
    }

    /**
     * Sets a query parameter with given name and value.
     *
     * @param string $parameterName
     *
     * @param mixed $value
     *
     * @return Query $value
     */
    public function setParameter(
        $parameterName,
        $value
    ) {
        $this->parameters
            ->set(
                $parameterName,
                $value
            );

        return $this;
    }
}
