<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Manager;

use Calam\Dbal\DatabaseConnectionInterface;

interface DatabaseConnectionManagerInterface
{
    /**
     * @param string $name
     *
     * @return DatabaseConnectionInterface
     */
    public function getConnectionWithName($name);
}
