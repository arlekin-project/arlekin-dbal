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
final class MissingTableIndexException extends PdoMySqlDriverException
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $indexName;

    /**
     * Constructor.
     *
     * @param string $tableName
     * @param string $indexName
     */
    public function __construct(string $tableName, string $indexName)
    {
        $this->tableName = $tableName;
        $this->indexName = $indexName;

        parent::__construct(sprintf(
            'Table "%s" has no index with name "%s".',
            $tableName,
            $indexName
        ));
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getIndexName(): string
    {
        return $this->indexName;
    }
}
