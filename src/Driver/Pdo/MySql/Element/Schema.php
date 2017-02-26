<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\Exception\NoElementFoundWithNameInSchemaException;

/**
 * MySQL schema / database.
 *
 * @see https://dev.mysql.com/doc/refman/5.5/en/glossary.html#glos_schema
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class Schema
{
    /**
     * @var array
     */
    private $tables;

    /**
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
     * @return array
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @param array $tables
     *
     * @return Schema
     */
    public function setTables(array $tables): Schema
    {
        $this->tables = $tables;

        return $this;
    }

    /**
     * @param Table $table
     *
     * @return Schema
     */
    public function addTable(Table $table): Schema
    {
        $this->tables[] = $table;

        return $this;
    }

    /**
     * @param int $index
     *
     * @return Schema
     */
    public function removeTableAtIndex(int $index): Schema
    {
        unset($this->tables[$index]);

        return $this;
    }

    /**
     * @return array
     */
    public function getViews(): array
    {
        return $this->views;
    }

    /**
     * @param array $views
     *
     * @return Schema
     */
    public function setViews(array $views): Schema
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @param View $view
     *
     * @return Schema
     */
    public function addView(View $view): Schema
    {
        $this->views[] = $view;

        return $this;
    }

    /**
     * @param int $index
     *
     * @return Schema
     */
    public function removeViewAtIndex(int $index): Schema
    {
        unset($this->views[$index]);

        return $this;
    }

    /**
     * @todo Move the toArray responsibility away from the Schemas
     *
     * @return array
     */
    public function toArray(): array
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

            unset($table);
        }

        foreach ($views as $view) {
            /* @var $view View */

            $arr['views'][] = $view->toArray();

            unset($view);
        }

        return $arr;
    }

    /**
     * @param string $name
     *
     * @return Table
     */
    public function getTableWithName(string $name): Table
    {
        $tables = $this->getTables();

        return $this->doGetWithName($tables, 'table', $name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasTableWithName(string $name): bool
    {
        $tables = $this->getTables();

        return $this->doHasWithName($tables, $name);
    }

    /**
     * @param string $name
     *
     * @return View
     */
    public function getViewWithName(string $name): View
    {
        $views = $this->getViews();

        return $this->doGetWithName($views, 'view', $name, false);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasViewWithName(string $name): bool
    {
        $views = $this->getViews();

        return $this->doHasWithName($views, $name, false);
    }

    /**
     * Gets a single named element from a given collection, given its name.
     *
     * @param array $collection
     * @param string $elementTypeName
     * @param string $elementName
     * @param bool $caseSensitive whether the comparison has to be case sensitive or not
     *
     * @return object the searched named element
     *
     * @throws NoElementFoundWithNameInSchemaException if no element is found
     */
    private function doGetWithName(array $collection, string $elementTypeName, string $elementName, bool $caseSensitive = true): object
    {
        $elementWithName = null;

        if ($caseSensitive) {
            foreach ($collection as $element) {
                if ($element->getName() === $elementName) {
                    $elementWithName = $element;
                }

                unset($element);
            }
        } else {
            foreach ($collection as $element) {
                if (strtolower($element->getName()) === strtolower($elementName)) {
                    $elementWithName = $element;
                }

                unset($element);
            }
        }

        if (null === $elementWithName) {
            throw new NoElementFoundWithNameInSchemaException($elementTypeName, $elementName);
        }

        return $elementWithName;
    }

    /**
     * Whether the given collection has an element with given name.
     *
     * @param array $collection
     * @param string $elementName
     * @param bool $caseSensitive whether the comparison has to be case sensitive or not
     *
     * @return bool
     */
    private function doHasWithName(array $collection, string $elementName, bool $caseSensitive = true): bool
    {
        $has = false;

        if ($caseSensitive) {
            foreach ($collection as $element) {
                if ($element->getName() === $elementName) {
                    $has = true;
                }

                unset($element);
            }
        } else {
            foreach ($collection as $element) {
                if (strtolower($element->getName()) === strtolower($elementName)) {
                    $has = true;
                }

                unset($element);
            }
        }

        return $has;
    }
}
