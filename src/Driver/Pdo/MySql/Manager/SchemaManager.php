<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Manager;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\View;
use Arlekin\Dbal\Driver\Pdo\MySql\Exception\PdoMySqlDriverException;

/**
 * To manage MySQL schemas.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class SchemaManager
{
    /**
     * Gets the table with given name from given schema.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return Table
     *
     * @throws PdoMySqlDriverException if no table with given name is found
     */
    public function getTableWithName(Schema $schema, $name)
    {
        $tables = $schema->getTables();

        $tableWithName = $this->doGetWithName($tables, 'table', $name);

        return $tableWithName;
    }

    /**
     * Whether the given Schema instance has a table with given name.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return bool
     */
    public function hasTableWithName(Schema $schema, $name)
    {
        $tables = $schema->getTables();

        $has = $this->doHasWithName($tables, $name);

        return $has;
    }

    /**
     * Removes the table with given name from given Schema instance.
     * Note that the table has to exists.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return SchemaManagerInterface
     *
     * @throws PdoMySqlDriverException if no table with given name is found
     */
    public function removeTableWithName(Schema $schema, $name)
    {
        foreach ($schema->getTables() as $i => $table) {
            if ($table->getName() === $name) {
                $schema->removeTableAtIndex($i);

                return $this;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Cannot remove table with name "%s": no such table in schema.',
                $name
            )
        );
    }

    /**
     * Gets the view with given name from given schema.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return View
     *
     * @throws PdoMySqlDriverException if no view with given name is found
     */
    public function getViewWithName(Schema $schema, $name)
    {
        $views = $schema->getViews();

        $viewWithName = $this->doGetWithName($views, 'view', $name, false);

        return $viewWithName;
    }

    /**
     * Whether the given Schema instance has a view with given name.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return bool
     */
    public function hasViewWithName(Schema $schema, $name)
    {
        $views = $schema->getViews();

        $has = $this->doHasWithName($views, $name, false);

        return $has;
    }

    /**
     * Removes the view with given name from given Schema instance.
     * Note that the view has to exists.
     *
     * @param Schema $schema
     * @param string $name
     *
     * @return SchemaManagerInterface
     *
     * @throws PdoMySqlDriverException if no view with given name is found
     */
    public function removeViewWithName(Schema $schema, $name)
    {
        foreach ($schema->getViews() as $i => $view) {
            if ($view->getName() === $name) {
                $schema->removeViewAtIndex($i);

                return $this;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Cannot remove view with name "%s": no such view in schema.',
                $name
            )
        );
    }

    /**
     * Gets a single named element from a given collection, given its name.
     *
     * @param array $collection
     * @param string $elementTypeName
     * @param string $name
     * @param bool $caseSensitive whether the comparison has to be case sensitive or not
     *
     * @return object the searched named element
     *
     * @throws PdoMySqlDriverException if no element is found
     */
    protected function doGetWithName(array $collection, $elementTypeName, $name, $caseSensitive = true)
    {
        $elementWithName = null;

        if ($caseSensitive) {
            foreach ($collection as $element) {
                $elementName = $element->getName();

                if ($elementName === $name) {
                    $elementWithName = $element;
                }
            }
        } else {
            foreach ($collection as $element) {
                $elementName = $element->getName();

                if (strtolower($elementName) === strtolower($name)) {
                    $elementWithName = $element;
                }
            }
        }

        if ($elementWithName === null) {
            throw new PdoMySqlDriverException(
                sprintf(
                    'Found no %s with name "%s" in schema.',
                    $elementTypeName,
                    $name
                )
            );
        }

        return $elementWithName;
    }

    /**
     * Whether the given collection has an element with given name.
     *
     * @param array $collection
     * @param string $name
     * @param bool $caseSensitive whether the comparison has to be case sensitive or not
     *
     * @return boolean
     */
    protected function doHasWithName(array $collection, $name, $caseSensitive = true)
    {
        $has = false;

        if ($caseSensitive) {
            foreach ($collection as $element) {
                $elementName = $element ->getName();

                if ($elementName === $name) {
                    $has = true;
                }
            }
        } else {
            foreach ($collection as $element) {
                $elementName = $element ->getName();

                if (strtolower($elementName) === strtolower($name)) {
                    $has = true;
                }
            }
        }

        return $has;
    }
}
