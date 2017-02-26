<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element\Exception;

use Calam\Dbal\Driver\Pdo\MySql\Exception\DriverException;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class MissingTableColumnException extends DriverException
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $columnName;

    /**
     * Constructor.
     *
     * @param string $tableName
     * @param string $columnName
     */
    public function __construct(string $tableName, string $columnName)
    {
        $this->tableName = $tableName;
        $this->columnName = $columnName;

        parent::__construct(sprintf(
            'Table "%s" has no column with name "%s".',
            $tableName,
            $columnName
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
    public function getColumnName(): string
    {
        return $this->columnName;
    }
}
