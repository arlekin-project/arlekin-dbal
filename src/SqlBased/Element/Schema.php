<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\SqlBased\Element;

use Arlekin\Core\Collection\ArrayCollection;

/**
 * Represents a SQL database.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
abstract class Schema
{
    /**
     * The schema's tables.
     *
     * @var ArrayCollection
     */
    protected $tables;

    /**
     * The schema's views.
     *
     * @var ArrayCollection
     */
    protected $views;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tables = new ArrayCollection();
        $this->views = new ArrayCollection();
    }

    /**
     * Gets the schema's tables.
     *
     * @return ArrayCollection
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Sets the schema's tables.
     *
     * @param array|ArrayCollection $tables
     *
     * @return Schema
     */
    public function setTables(
        $tables
    ) {
        $this
            ->tables
            ->replaceWithCollection(
                $tables
            );

        return $this;
    }

    /**
     * Adds a table to the schema's tables.
     *
     * @param Table $table
     *
     * @return Schema
     */
    public function addTable(
        Table $table
    ) {
        $this
            ->tables
            ->add(
                $table
            );

        return $this;
    }

    /**
     * Adds tables to the schema's tables.
     *
     * @param array|ArrayCollection $tables
     *
     * @return Schema
     */
    public function addTables(
        $tables
    ) {
        $this
            ->tables
            ->mergeWithCollections(
                $tables
            );

        return $this;
    }

    /**
     * Gets the schema's views.
     *
     * @return ArrayCollection
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Sets the schema's views.
     *
     * @param array|ArrayCollection $views
     *
     * @return Schema
     */
    public function setViews(
        $views
    ) {
        $this
            ->views
            ->replaceWithCollection(
                $views
            );

        return $this;
    }

    /**
     * Adds a view to the schema's views.
     *
     * @param View $view
     *
     * @return Schema
     */
    public function addView(
        View $view
    ) {
        $this
            ->views
            ->add(
                $view
            );

        return $this;
    }

    /**
     * Adds views to the schema's views.
     *
     * @param array|ArrayCollection $views
     *
     * @return Schema
     */
    public function addViews(
        $views
    ) {
        $this
            ->views
            ->mergeWithCollections(
                $views
            );

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
        $arr = array(
            'tables' => array(),
            'views' => array(),
        );
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
}