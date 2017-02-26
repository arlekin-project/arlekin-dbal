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
     * @param string $name
     * @param string $definition
     */
    public function __construct(string $name, string $definition)
    {
        $this->name = $name;
        $this->definition = $definition;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDefinition(): string
    {
        return $this->definition;
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
