<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer;

interface DriverInterface
{
    /**
     * @param array &$parameters
     */
    public function instanciateDatabaseConnection(array &$parameters);
}
