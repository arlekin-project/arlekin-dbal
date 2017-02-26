<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element\Exception;

use Calam\Dbal\Driver\Pdo\MySql\Exception\PdoMySqlDriverException;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class NoElementFoundWithNameInSchemaException extends PdoMySqlDriverException
{
    /**
     * @var string
     */
    private $elementTypeName;

    /**
     * @var string
     */
    private $elementName;

    /**
     * @param string $elementTypeName
     * @param string $elementName
     */
    public function __construct(string $elementTypeName, string $elementName)
    {
        $this->elementTypeName = $elementTypeName;
        $this->elementName = $elementName;

        parent::__construct(
            sprintf(
                'Found no %s with name "%s" in schema.',
                $elementTypeName,
                $elementName
            )
        );
    }

    /**
     * @return string
     */
    public function getElementName(): string
    {
        return $this->elementName;
    }
}
