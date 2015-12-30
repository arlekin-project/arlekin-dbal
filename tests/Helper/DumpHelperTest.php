<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Helper;

use Arlekin\Dbal\Helper\DumpHelper;
use Arlekin\Dbal\Tests\Helper\CommonTestHelper;

class DumpHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\Dbal\Helper\DumpHelper::dumpValue
     */
    public function testDumpValue()
    {
        $this->assertSame(
            'null',
            DumpHelper::dumpValue(null)
        );
        $this->assertSame(
            '\'test\'',
            DumpHelper::dumpValue('test')
        );
        $this->assertSame(
            'true',
            DumpHelper::dumpValue(true)
        );
        $this->assertSame(
            'false',
            DumpHelper::dumpValue(false)
        );
        $this->assertSame(
            '[]',
            DumpHelper::dumpValue([])
        );
        $this->assertSame(
            '42',
            DumpHelper::dumpValue(42)
        );
        CommonTestHelper::assertExceptionThrown(
            function () {
                DumpHelper::dumpValue(
                    tmpfile()
                );
            },
            \Exception::class,
            'Given "resource" cannot be dumped.'
        );
        CommonTestHelper::assertExceptionThrown(
            function () {
                DumpHelper::dumpValue(
                    new \stdClass()
                );
            },
            \Exception::class,
            'Given "object" cannot be dumped.'
        );
    }

    /**
     * @covers Arlekin\Dbal\Helper\DumpHelper::dumpArray
     */
    public function testDumpArray()
    {
        $this->assertSame(
            '[]',
            DumpHelper::dumpArray(
                []
            )
        );

        $this->assertSame(
            '[' . PHP_EOL
            .'    0 => 1,' . PHP_EOL
            .']',
            DumpHelper::dumpArray(
                [
                    1,
                ]
            )
        );

        $this->assertSame(
            '[' . PHP_EOL
            .'    0 => 1,' . PHP_EOL
            .'    1 => 2,'. PHP_EOL
            .']',
            DumpHelper::dumpArray(
                [
                    1,
                    2,
                ]
            )
        );

        $this->assertSame(
            '[' . PHP_EOL
            .'    0 => 1,' . PHP_EOL
            .'    1 => ['. PHP_EOL
            .'        0 => 1,'. PHP_EOL
            .'        1 => 2,'. PHP_EOL
            .'    ],'. PHP_EOL
            .']',
            DumpHelper::dumpArray(
                [
                    1,
                    [
                        1,
                        2,
                    ],
                ]
            )
        );
    }
}
