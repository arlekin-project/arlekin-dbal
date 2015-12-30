<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Element;

use Arlekin\Dbal\SqlBased\Element\ForeignKey as BaseForeignKey;

/**
 * Represents a MySQL foreign key.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ForeignKey extends BaseForeignKey
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->onDelete = ForeignKeyOnDeleteConstraint::ON_DELETE_RESTRICT;
        $this->onUpdate = ForeignKeyOnUpdateConstraint::ON_UPDATE_RESTRICT;
    }
}
