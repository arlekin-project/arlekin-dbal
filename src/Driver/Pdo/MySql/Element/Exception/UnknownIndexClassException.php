<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element\Exception;

use Calam\Dbal\Driver\Pdo\MySql\Element\IndexClasses;
use Calam\Dbal\Driver\Pdo\MySql\Exception\DriverException;

/**
 * Exception to be thrown when an unknown index class is used.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class UnknownIndexClassException extends DriverException
{
    /**
     * @var string
     */
    private $indexClass;

    /**
     * Constructor.
     *
     * @param string $indexClass
     */
    public function __construct(string $indexClass)
    {
        parent::__construct(
            sprintf(
                'Unknown index class "%s". Known index classes are %s.',
                $indexClass,
                IndexClasses::$KNOWN
            )
        );

        $this->indexClass = $indexClass;
    }

    /**
     * @return string
     */
    public function getIndexClass(): string
    {
        return $this->indexClass;
    }
}
