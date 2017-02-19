<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Element;

/**
 * Represents a MySQL foreign key on delete constraint.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ForeignKeyOnDeleteConstraint
{
    const ON_DELETE_CASCADE = 'CASCADE';
    const ON_DELETE_SET_NULL = 'SET NULL';
    const ON_DELETE_NO_ACTION = 'NO ACTION';
    const ON_DELETE_RESTRICT = 'RESTRICT';

    /**
     * The allowed on delete constraints.
     *
     * @var array
     */
    public static $authorizedOnDelete = [
        self::ON_DELETE_CASCADE,
        self::ON_DELETE_SET_NULL,
        self::ON_DELETE_NO_ACTION,
        self::ON_DELETE_RESTRICT,
    ];
}
