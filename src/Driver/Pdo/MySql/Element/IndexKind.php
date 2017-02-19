<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * Represents a MySQL index kind.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class IndexKind
{
    const KIND_BTREE = 'BTREE';
    const KIND_HASH = 'HASH';
    const KIND_UNIQUE = 'UNIQUE';
    const KIND_FULLTEXT = 'FULLTEXT';
    const KIND_SPATIAL = 'SPATIAL';

    /**
     * The allowed index kinds.
     *
     * @var array
     */
    public static $allowedKinds = [
        self::KIND_BTREE,
        self::KIND_HASH,
        self::KIND_UNIQUE,
        self::KIND_FULLTEXT,
        self::KIND_SPATIAL,
    ];
}
