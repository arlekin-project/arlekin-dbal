<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\SqlBased\Manager;

use Arlekin\DatabaseAbstractionLayer\Exception\DbalException;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Schema;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\Table;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\View;

/**
 * To manage SQL-based schemas.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
interface SchemaManagerInterface
{
    /**
     * Gets the table with given name from given schema.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return Table
     *
     * @throws DbalException if no table with given name is found
     */
    public function getTableWithName(Schema $schema, $name);

    /**
     * Whether the given Schema instance has a table with given name.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return bool
     */
    public function hasTableWithName(Schema $schema, $name);

    /**
     * Removes the table with given name from given Schema instance.
     * Note that the table has to exists.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return SchemaManagerInterface
     *
     * @throws DbalException if no table with given name is found
     */
    public function removeTableWithName(Schema $schema, $name);

    /**
     * Gets the view with given name from given schema.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return View
     *
     * @throws DbalException if no view with given name is found
     */
    public function getViewWithName(Schema $schema, $name);

    /**
     * Whether the given Schema instance has a view with given name.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return bool
     */
    public function hasViewWithName(Schema $schema, $name);

    /**
     * Removes the view with given name from given Schema instance.
     * Note that the view has to exists.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return SchemaManagerInterface
     *
     * @throws DbalException if no view with given name is found
     */
    public function removeViewWithName(Schema $schema, $name);
}