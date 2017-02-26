<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * MySQL foreign key on update reference options.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class ForeignKeyOnUpdateReferenceOptions
{
    const ON_UPDATE_RESTRICT = 'RESTRICT';
    const ON_UPDATE_CASCADE = 'CASCADE';
    const ON_UPDATE_SET_NULL = 'SET NULL';
    const ON_UPDATE_NO_ACTION = 'NO ACTION';
    const ON_UPDATE_SET_DEFAULT = 'SET DEFAULT';

    /**
     * @var array
     */
    public static $KNOWN = [
        self::ON_UPDATE_RESTRICT,
        self::ON_UPDATE_CASCADE,
        self::ON_UPDATE_SET_NULL,
        self::ON_UPDATE_NO_ACTION,
        self::ON_UPDATE_SET_DEFAULT,
    ];
}
