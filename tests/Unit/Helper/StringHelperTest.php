<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Unit\Helper;

use Arlekin\Dbal\Helper\StringHelper;
use Arlekin\Dbal\Tests\AbstractBaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class StringHelperTest extends AbstractBaseTest
{
    /**
     * @covers Arlekin\Dbal\Helper\StringHelper::startsWith
     */
    public function testStartsWithTrue()
    {
        $startsWith = StringHelper::startsWith('testFunction', 'test');

        $this->assertEquals(true, $startsWith);
    }

    /**
     * @covers Arlekin\Dbal\Helper\StringHelper::startsWith
     */
    public function testStartsWithFalse()
    {
        $startsWith = StringHelper::startsWith('function_test', 'test');

        $this->assertEquals(false, $startsWith);
    }

    /**
     * @covers Arlekin\Dbal\Helper\StringHelper::endsWith
     */
    public function testEndsWithTrue()
    {
        $endsWith = StringHelper::endsWith('function_test', 'test');

        $this->assertEquals(true, $endsWith);
    }

    /**
     * @covers Arlekin\Dbal\Helper\StringHelper::endsWith
     */
    public function testEndsWithFalse()
    {
        $endsWith = StringHelper::endsWith('testFunctions', 'test');

        $this->assertEquals(false, $endsWith);
    }
}
