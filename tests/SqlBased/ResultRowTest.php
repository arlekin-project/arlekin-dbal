<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\Tests\SqlBased;

use Arlecchino\DatabaseAbstractionLayer\SqlBased\ResultRow;
use Arlecchino\Core\Tests\Helper\CommonTestHelper;
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
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\ResultRow::getData
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\ResultRow::setData
     */
    public function testGetAndSetData()
    {
        $data = array(
            'test' => 42
        );

        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->resultRow,
            'data',
            $data
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\ResultRow::get
     */
    public function testGet()
    {
        $data = array(
            'test' => 42
        );
        $this->resultRow->setData(
            $data
        );
        
        $this->assertSame(
            42,
            $this->resultRow->get(
                'test'
            )
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