<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * MySQL index type.
 *
 * @see https://dev.mysql.com/doc/refman/5.5/en/create-index.html#create-index-storage-engine-index-types
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class IndexType
{
    const BTREE = 'BTREE';
    const HASH = 'HASH';

    /**
     * Allowed index types.
     *
     * @var array
     */
    public static $known = [
        self::BTREE,
        self::HASH
    ];
}
