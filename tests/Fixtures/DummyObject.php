<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Fixtures;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class DummyObject
{
    protected $testProperty;

    public function getTestProperty()
    {
        return $this->testProperty;
    }
}