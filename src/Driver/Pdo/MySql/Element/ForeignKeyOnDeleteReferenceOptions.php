<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * MySQL foreign key on delete reference options.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class ForeignKeyOnDeleteReferenceOptions
{
    const ON_DELETE_RESTRICT = 'RESTRICT';
    const ON_DELETE_CASCADE = 'CASCADE';
    const ON_DELETE_SET_NULL = 'SET NULL';
    const ON_DELETE_NO_ACTION = 'NO ACTION';
    const ON_DELETE_SET_DEFAULT = 'SET DEFAULT';

    /**
     * @var array
     */
    public static $KNOWN = [
        self::ON_DELETE_RESTRICT,
        self::ON_DELETE_CASCADE,
        self::ON_DELETE_SET_NULL,
        self::ON_DELETE_NO_ACTION,
        self::ON_DELETE_SET_DEFAULT,
    ];
}
