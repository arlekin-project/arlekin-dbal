<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Migration\SqlBased\Builder;

use Arlekin\Dbal\SqlBased\Element\Schema;

/**
 * Builds SQL-based migration queries.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
interface MigrationQueriesBuilderInterface
{
    /**
     * Builds and gets the SQL-based queries to migrate
     * from the current database schema to a given destination schema.
     *
     * @param Schema $sourceSchema
     * @param Schema $destinationSchema
     *
     * @return array a collection of queries
     */
    public function getMigrationSqlQueries(Schema $sourceSchema, Schema $destinationSchema);
}