<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Manager;

use Arlekin\Dbal\Driver\Pdo\MySql\Exception\PdoMySqlDriverException;
use Arlekin\Dbal\SqlBased\Element\Schema;
use Arlekin\Dbal\SqlBased\Manager\SchemaManagerInterface;

/**
 * To manage MySQL schemas.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class SchemaManager implements SchemaManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTableWithName(Schema $schema, $name)
    {
        $tables = $schema->getTables();

        $tableWithName = $this->doGetWithName($tables, 'table', $name);

        return $tableWithName;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTableWithName(Schema $schema, $name)
    {
        $tables = $schema->getTables();

        $has = $this->doHasWithName($tables, $name);

        return $has;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getViewWithName(Schema $schema, $name)
    {
        $views = $schema->getViews();

        $viewWithName = $this->doGetWithName($views, 'view', $name, false);

        return $viewWithName;
    }

    /**
     * {@inheritdoc}
     */
    public function hasViewWithName(Schema $schema, $name)
    {
        $views = $schema->getViews();

        $has = $this->doHasWithName($views, $name, false);

        return $has;
    }

    /**
     * {@inheritdoc}
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
