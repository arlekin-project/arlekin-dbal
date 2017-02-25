<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element\Exception;

use Calam\Dbal\Driver\Pdo\MySql\Element\IndexType;
use Calam\Dbal\Driver\Pdo\MySql\Exception\PdoMySqlDriverException;

/**
 * To be thrown when an unknown index class is used.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class UnknownIndexTypeException extends PdoMySqlDriverException
{
    /**
     * @var string
     */
    private $indexType;

    /**
     * UnknownIndexClassException constructor.
     *
     * @param string $indexType
     */
    public function __construct(string $indexType)
    {
        parent::__construct(
            sprintf(
                'Unknown index class "%s". Known index types are %s.',
                $indexType,
                IndexType::$known
            )
        );

        $this->indexType = $indexType;
    }

    /**
     * @return string
     */
    public function getIndexType(): string
    {
        return $this->indexType;
    }
}