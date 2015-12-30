<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Element;

/**
 * Represents a MySQL column type
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ColumnType
{
    const TYPE_TINYINT = 'TINYINT';
    const TYPE_SMALLINT = 'SMALLINT';
    const TYPE_MEDIUMINT = 'MEDIUMINT';
    const TYPE_INT = 'INT';
    const TYPE_BIGINT = 'BIGINT';

    const TYPE_FLOAT = 'FLOAT';

    const TYPE_DATE = 'DATE';
    const TYPE_DATETIME = 'DATETIME';
    const TYPE_TIMESTAMP = 'TIMESTAMP';
    const TYPE_TIME = 'TIME';
    const TYPE_YEAR = 'YEAR';

    const TYPE_CHAR = 'CHAR';
    const TYPE_VARCHAR = 'VARCHAR';
    const TYPE_BINARY = 'BINARY';
    const TYPE_VARBINARY = 'VARBINARY';
    const TYPE_TINYTEXT = 'TINYTEXT';
    const TYPE_TEXT = 'TEXT';
    const TYPE_MEDIUMTEXT = 'MEDIUMTEXT';
    const TYPE_LONGTEXT = 'LONGTEXT';
    const TYPE_TINYBLOB = 'TINYBLOB';
    const TYPE_BLOB = 'BLOB';
    const TYPE_MEDIUMBLOB = 'MEDIUMBLOB';
    const TYPE_LONGBLOB = 'LONGBLOB';

    const TYPE_ENUM = 'ENUM';

    const TYPE_SET = 'SET';

    /**
     * The MySQL column types representing integers.
     *
     * @var array
     */
    public static $EXACT_TYPES = [
        self::TYPE_TINYINT,
        self::TYPE_SMALLINT,
        self::TYPE_MEDIUMINT,
        self::TYPE_INT,
        self::TYPE_BIGINT,
    ];

    /**
     * The MySQL column types representing dates and times.
     *
     * @var array
     */
    public static $DATE_AND_TIME_TYPES = [
        self::TYPE_DATE,
        self::TYPE_DATETIME,
        self::TYPE_TIMESTAMP,
        self::TYPE_TIME,
        self::TYPE_YEAR,
    ];

    /**
     * The MySQL column types representing strings.
     *
     * @var array
     */
    public static $STRING_TYPE = [
        self::TYPE_CHAR,
        self::TYPE_VARCHAR,
        self::TYPE_BINARY,
        self::TYPE_VARBINARY,
        self::TYPE_TINYTEXT,
        self::TYPE_TEXT,
        self::TYPE_MEDIUMTEXT,
        self::TYPE_LONGTEXT,
        self::TYPE_TINYBLOB,
        self::TYPE_BLOB,
        self::TYPE_MEDIUMBLOB,
        self::TYPE_LONGBLOB,
    ];

    /**
     * The MySQL column types for which a length can be specified.
     *
     * @var array
     */
    public static $WITH_LENGTH_TYPES = [
        self::TYPE_TINYINT,
        self::TYPE_SMALLINT,
        self::TYPE_MEDIUMINT,
        self::TYPE_INT,
        self::TYPE_BIGINT,
        self::TYPE_CHAR,
        self::TYPE_VARCHAR,
        self::TYPE_BINARY,
        self::TYPE_VARBINARY,
    ];
}
