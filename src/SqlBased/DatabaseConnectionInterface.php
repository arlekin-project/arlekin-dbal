<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\SqlBased;

/**
 * Represents a database connection.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
interface DatabaseConnectionInterface
{
    /**
     * True if connected to the database, false otherwise.
     *
     * @return boolean
     */
    public function isConnected();

    /**
     * Connects with the database.
     *
     * @throws Exception if already connected
     *
     * @return DatabaseConnectionInterface
     */
    public function connect();

    /**
     * Connects with the database if not already connected.
     * It should not fail if already connected, as it does check
     * if already connected before trying to connect.
     *
     * @return DatabaseConnectionInterface
     */
    public function connectIfNotConnected();

    /**
     * Disconnects from the database.
     *
     * @throws Exception if already disconnected
     *
     * @return DatabaseConnectionInterface
     */
    public function disconnect();

    /**
     * Executes a given query.
     *
     * @param mixed $query
     *
     * @return array
     */
    public function executeQuery($query);

    /**
     * Executes given query.
     *
     * @param array $queries
     *
     * @return array
     */
    public function executeMultipleQueries(array $queries);
}
