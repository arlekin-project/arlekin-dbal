<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * MySQL column data type.
 *
 * @see https://dev.mysql.com/doc/refman/5.5/en/data-types.html
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class ColumnDataTypes
{
    /**
     * Integer types
     */
    const TYPE_TINYINT = 'TINYINT';
    const TYPE_SMALLINT = 'SMALLINT';
    const TYPE_MEDIUMINT = 'MEDIUMINT';
    const TYPE_INT = 'INT';
    const TYPE_INTEGER = 'INTEGER';
    const TYPE_BIGINT = 'BIGINT';

    /**
     * Fixed-point types
     */
    const TYPE_DECIMAL = 'DECIMAL';
    const TYPE_NUMERIC = 'NUMERIC';

    /**
     * Floating-point types
     */
    const TYPE_FLOAT = 'FLOAT';
    const TYPE_REAL = 'REAL';
    const TYPE_DOUBLE_PRECISION = 'DOUBLE PRECISION';

    /**
     * Bit-value types
     */
    const TYPE_BIT = 'BIT';

    /**
     * Date and time types
     */
    const TYPE_DATE = 'DATE';
    const TYPE_DATETIME = 'DATETIME';
    const TYPE_TIMESTAMP = 'TIMESTAMP';
    const TYPE_TIME = 'TIME';
    const TYPE_YEAR = 'YEAR';

    /**
     * String types
     */
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
     * @var array
     */
    public static $INTEGER_TYPES = [
        self::TYPE_TINYINT,
        self::TYPE_SMALLINT,
        self::TYPE_MEDIUMINT,
        self::TYPE_INT,
        self::TYPE_INTEGER,
        self::TYPE_BIGINT,
    ];

    /**
     * @var array
     */
    public static $FIXED_POINT_TYPES = [
        self::TYPE_DECIMAL,
        self::TYPE_NUMERIC,
    ];

    /**
     * @var array
     */
    public static $FLOATING_POINT_TYPES = [
        self::TYPE_FLOAT,
        self::TYPE_REAL,
        self::TYPE_DOUBLE_PRECISION,
    ];

    /**
     * @var array
     */
    public static $BIT_TYPES = [
        self::TYPE_BIT,
    ];

    /**
     * @var array
     */
    public static $NUMERIC_TYPES = [
        self::TYPE_TINYINT,
        self::TYPE_SMALLINT,
        self::TYPE_MEDIUMINT,
        self::TYPE_INT,
        self::TYPE_INTEGER,
        self::TYPE_BIGINT,
        self::TYPE_DECIMAL,
        self::TYPE_NUMERIC,
        self::TYPE_FLOAT,
        self::TYPE_REAL,
        self::TYPE_DOUBLE_PRECISION,
        self::TYPE_BIT,
    ];

    /**
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
     * @see https://dev.mysql.com/doc/refman/5.5/en/string-types.html
     *
     * @var array
     */
    public static $STRING_TYPE = [
        self::TYPE_CHAR,
        self::TYPE_VARCHAR,
        self::TYPE_BINARY,
        self::TYPE_VARBINARY,
        self::TYPE_TINYBLOB,
        self::TYPE_BLOB,
        self::TYPE_MEDIUMBLOB,
        self::TYPE_LONGBLOB,
        self::TYPE_TINYTEXT,
        self::TYPE_TEXT,
        self::TYPE_MEDIUMTEXT,
        self::TYPE_LONGTEXT,
        self::TYPE_ENUM,
        self::TYPE_SET,
    ];

    /**
     * Column types for which a length can be specified.
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
