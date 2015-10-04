<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Tests\SqlBased;

use Arlekin\Core\Tests\Helper\CommonTestHelper;
use Arlekin\DatabaseAbstractionLayer\SqlBased\ResultRow;
use Arlekin\DatabaseAbstractionLayer\SqlBased\ResultSet;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ResultSetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ResultSet
     */
    protected $resultSet;

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\ResultSet::__construct
     */
    public function testConstruct()
    {
        $resultSet = new ResultSet();

        $this->assertAttributeSame([], 'rows', $resultSet);
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\ResultSet::getRows
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\ResultSet::setRows
     */
    public function testGetAndSetRows()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->resultSet,
            'rows',
            [
                new ResultRow(),
                new ResultRow(),
                new ResultRow(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->resultSet = new ResultSet();
    }
}