<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * MySQL view.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class View
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $definition;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return View
     */
    public function setName(string $name): View
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefinition(): string
    {
        return $this->definition;
    }

    /**
     * @param string $definition
     *
     * @return View
     */
    public function setDefinition(string $definition): View
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * @todo Move the toArray responsibility away from the View
     *
     * @return array
     */
    public function toArray(): array
    {
        $arr = [
            'name' => $this->getName(),
            'definition' => $this->getDefinition(),
        ];

        return $arr;
    }
}
