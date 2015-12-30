<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Tests\DatabaseAbstractionLayer;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnDeleteConstraint;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnUpdateConstraint;
use Arlekin\Dbal\Driver\Pdo\MySql\Tests\AbstractBasePdoMySqlTest;

class ForeignKeyTest extends AbstractBasePdoMySqlTest
{
    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey::__construct
     */
    public function testConstruct()
    {
        $foreignKey = new ForeignKey();

        $this->assertAttributeSame(
            ForeignKeyOnDeleteConstraint::ON_DELETE_RESTRICT,
            'onDelete',
            $foreignKey
        );

        $this->assertAttributeSame(
            ForeignKeyOnUpdateConstraint::ON_UPDATE_RESTRICT,
            'onUpdate',
            $foreignKey
        );
    }
}
