<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Unit\Helper;

use Arlekin\Dbal\Helper\ArrayHelper;
use Arlekin\Dbal\Tests\BaseTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ArrayHelperTest extends BaseTest
{
    /**
     * @covers Arlekin\Dbal\Helper\ArrayHelper::arrayDiffRecursive
     */
    public function testArrayDiffRecursive()
    {
        $tests = self::getTestArrayDiffRecursiveTests();

        foreach ($tests as $test) {
            $pair = $test['pair'];
            
            $expected = $test['expected'];

            $diff = ArrayHelper::arrayDiffRecursive($pair[0], $pair[1]);

            $this->assertEquals(
                $expected,
                $diff,
                'Failed test: "'
                .$test['name']
                .'".'
            );
        }
    }

    /**
     * @covers Arlekin\Dbal\Helper\ArrayHelper::arrayDiffRecursive
     */
    public function testArrayDiffRecursiveBooleanValue()
    {
        $orig = [
            'nullable' => true,
        ];

        $dest = [
            'nullable' => false,
        ];

        $this->assertEquals(
            [
                'nullable' => false,
            ],
            ArrayHelper::arrayDiffRecursive($dest, $orig)
        );

        $this->assertEquals(
            [
                'nullable' => true,
            ],
            ArrayHelper::arrayDiffRecursive($orig, $dest)
        );
    }

    /**
     * @covers Arlekin\Dbal\Helper\ArrayHelper::arrayDiffRecursive
     */
    public function testArrayDiffRecursiveComplex()
    {
        $orig = [
            'name' => 'birthDate',
            'type' => 'DATE',
            'nullable' => true,
            'parameters' => [
                'length' => null,
            ],
            'autoIncrement' => false,
            'table' => 'employees',
        ];

        $dest = [
            'name' => 'birthDate',
            'type' => 'DATE',
            'nullable' => false,
            'parameters' => [
                'length' => null,
            ],
            'autoIncrement' => false,
            'table' => 'employees',
        ];

        $this->assertEquals(
            [
                'nullable' => false,
            ],
            ArrayHelper::arrayDiffRecursive($dest, $orig)
        );
    }

    /**
     * @covers Arlekin\Dbal\Helper\ArrayHelper::arraysAreSameRecursive
     */
    public function testArraysAreSameRecursive()
    {
        $orig = [
            'name' => 'birthDate',
            'type' => 'DATE',
            'nullable' => true,
            'parameters' => [
                'length' => null,
            ],
            'autoIncrement' => false,
            'table' => 'employees',
        ];

        $dest = [
            'name' => 'birthDate',
            'type' => 'DATE',
            'nullable' => false,
            'parameters' => [
                'length' => null,
            ],
            'autoIncrement' => false,
            'table' => 'employees',
        ];

        $this->assertFalse(
            ArrayHelper::arraysAreSameRecursive($orig, $dest)
        );
    }

    /**
     * @return array
     */
    protected static function getTestArrayDiffRecursiveTests()
    {
        return [
            [
                'name' => 'Full integer index',
                'pair' => [
                    [
                        'a',
                        'b',
                    ],
                    [
                        'b',
                    ],
                ],
                'expected' => [
                    'a',
                ],
            ],
            [
                'name' => 'String index right not in left',
                'pair' => [
                    [
                        'a' => 'aa',
                    ],
                    [
                        'a' => 'bb',
                    ],
                ],
                'expected' => [
                    'a' => 'aa',
                ],
            ],
            [
                'name' => 'Mixed index index type right not in left',
                'pair' => [
                    [
                        'a',
                        'b',
                        'a' => 'aa',
                    ],
                    [
                        'a' => 'bb',
                        'b',
                    ],
                ],
                'expected' => [
                    'a',
                    'a' => 'aa',
                ],
            ],
            [
                'name' => 'Mixed index index type right not in left not same numeric index',
                'pair' => [
                    [
                        'b',
                        'a' => 'aa',
                        'a',
                    ],
                    [
                        'a' => 'bb',
                        'b',
                    ],
                ],
                'expected' => [
                    'a',
                    'a' => 'aa',
                ],
            ],
            [
                'name' => 'Nested with full integer indexes indexed by integer',
                'pair' => [
                    [
                        [
                            'a',
                            'b',
                        ],
                    ],
                    [
                        [
                            'b',
                        ],
                    ],
                ],
                'expected' => [
                    [
                        'a',
                    ],
                ],
            ],
            [
                'name' => 'Nested with full integer indexes indexed by string',
                'pair' => [
                    [
                        'a' => [
                            'a',
                            'b',
                        ],
                    ],
                    [
                        'a' => [
                            'b',
                        ],
                    ],
                ],
                'expected' => [
                    'a' => [
                        'a',
                    ],
                ],
            ],
        ];
    }
}
