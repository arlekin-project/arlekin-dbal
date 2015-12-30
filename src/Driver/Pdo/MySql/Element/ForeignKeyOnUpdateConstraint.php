<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Element;

/**
 * Represents a MySQL foreign key on update constraint.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ForeignKeyOnUpdateConstraint
{
    const ON_UPDATE_CASCADE = 'CASCADE';
    const ON_UPDATE_SET_NULL = 'SET NULL';
    const ON_UPDATE_NO_ACTION = 'NO ACTION';
    const ON_UPDATE_RESTRICT = 'RESTRICT';

    /**
     * The allowed on update constraints.
     *
     * @var array
     */
    public static $authorizedOnUpdate = [
        self::ON_UPDATE_CASCADE,
        self::ON_UPDATE_SET_NULL,
        self::ON_UPDATE_NO_ACTION,
        self::ON_UPDATE_RESTRICT,
    ];
}
