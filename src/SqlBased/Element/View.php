<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\SqlBased\Element;

/**
 * Represents a SQL view.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
abstract class View
{
    /**
     * The view's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The view's definition.
     *
     * @var string
     */
    protected $definition;

    /**
     * Gets the view's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the view's name.
     *
     * @param string $name
     *
     * @return View
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the view's definition.
     *
     * @return string
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Sets the view's definition.
     *
     * @param string $definition
     *
     * @return View
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * Converts the View instance to an array.
     *
     * @todo Move the toArray responsability away from the View
     *
     * @return array
     */
    public function toArray()
    {
        $arr = [
            'name' => $this->getName(),
            'definition' => $this->getDefinition(),
        ];

        return $arr;
    }
}
