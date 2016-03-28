<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Element;

use Arlekin\Dbal\Driver\Pdo\MySql\Exception\PdoMySqlDriverException;

/**
 * Represents a MySQL database.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class Schema
{
    /**
     * The schema's tables.
     *
     * @var array
     */
    private $tables;

    /**
     * The schema's views.
     *
     * @var array
     */
    private $views;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tables = [];
        $this->views = [];
    }

    /**
     * Gets the schema's tables.
     *
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Sets the schema's tables.
     *
     * @param array $tables
     *
     * @return Schema
     */
    public function setTables(array $tables)
    {
        $this->tables = $tables;

        return $this;
    }

    /**
     * @param Table $table
     *
     * @return Schema
     */
    public function addTable(Table $table)
    {
        $this->tables[] = $table;

        return $this;
    }

    /**
     * @param int $index
     *
     * @return Schema
     */
    public function removeTableAtIndex($index)
    {
        unset($this->tables[$index]);

        return $this;
    }

    /**
     * Gets the schema's views.
     *
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Sets the schema's views.
     *
     * @param array $views
     *
     * @return Schema
     */
    public function setViews(array $views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @param View $view
     *
     * @return Schema
     */
    public function addView(View $view)
    {
        $this->views[] = $view;

        return $this;
    }

    /**
     * @param int $index
     *
     * @return Schema
     */
    public function removeViewAtIndex($index)
    {
        unset($this->views[$index]);

        return $this;
    }

    /**
     * Converts a Schema into an array.
     *
     * @todo Move the toArray responsability away from the Schemas
     *
     * @return array
     */
    public function toArray()
    {
        $tables = $this->getTables();
        $views = $this->getViews();

        $arr = [
            'tables' => [],
            'views' => [],
        ];

        foreach ($tables as $table) {
            /* @var $table Table */
            $arr['tables'][] = $table->toArray();
        }

        foreach ($views as $view) {
            /* @var $view Views */
            $arr['views'][] = $view->toArray();
        }

        return $arr;
    }

    /**
     * Gets the table with given name.
     *
     * @param string $name
     *
     * @return Table
     *
     * @throws PdoMySqlDriverException if no table with given name is found
     */
    public function getTableWithName($name)
    {
        $tables = $this->getTables();

        $tableWithName = $this->doGetWithName($tables, 'table', $name);

        return $tableWithName;
    }

    /**
     * Whether the schema has a table with given name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasTableWithName($name)
    {
        $tables = $this->getTables();

        $has = $this->doHasWithName($tables, $name);

        return $has;
    }

    /**
     * Gets the view with given name.
     *
     * @param string $name
     *
     * @return View
     *
     * @throws PdoMySqlDriverException if no view with given name is found
     */
    public function getViewWithName($name)
    {
        $views = $this->getViews();

        $viewWithName = $this->doGetWithName($views, 'view', $name, false);

        return $viewWithName;
    }

    /**
     * Whether the schema has a view with given name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasViewWithName($name)
    {
        $views = $this->getViews();

        $has = $this->doHasWithName($views, $name, false);

        return $has;
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
    private function doGetWithName(array $collection, $elementTypeName, $name, $caseSensitive = true)
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
    private function doHasWithName(array $collection, $name, $caseSensitive = true)
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
