<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Migration\SqlBased\Builder;

use Arlekin\Dbal\SqlBased\DatabaseConnectionInterface;
use Arlekin\Dbal\SqlBased\Element\Schema;

/**
 * Builds a SqlBased\Element\Schema from an SQL-based database.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
interface SchemaBuilderInterface
{
    /**
     * Builds and gets a Schema from the SQL-based database
     * that the provided $connection is configured to use.
     *
     * @param DatabaseConnectionInterface $connection
     *
     * @return Schema
     */
    public function getFromDatabase(DatabaseConnectionInterface $connection);
}