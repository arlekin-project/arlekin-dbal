<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Tests\SqlBased;

use Arlekin\DatabaseAbstractionLayer\SqlBased\ResultRow;
use Arlekin\Core\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ResultRowTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ResultRow
     */
    protected $resultRow;

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\ResultRow::getData
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\ResultRow::setData
     */
    public function testGetAndSetData()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->resultRow,
            'data',
            [
                'test' => 42,
            ]
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\ResultRow::get
     */
    public function testGet()
    {
        $data = [
            'test' => 42,
        ];

        $this->resultRow->setData($data);

        $this->assertSame(
            42,
            $this->resultRow->get('test')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->resultRow = new ResultRow();
    }
}