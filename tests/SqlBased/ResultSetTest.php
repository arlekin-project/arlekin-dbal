<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\Tests\SqlBased;

use Arlecchino\Core\Collection\ArrayCollection;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\ResultRow;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\ResultSet;
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
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\ResultSet::__construct
     */
    public function testConstruct()
    {
        $resultSet = new ResultSet();

        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'rows',
            $resultSet
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\ResultSet::getRows
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\ResultSet::setRows
     */
    public function testGetAndSetRows()
    {
        $rows = array(
            new ResultRow(),
            new ResultRow(),
            new ResultRow()
        );
        $initRows = $this->resultSet->getRows();
        $result = $this->resultSet->setRows(
            $rows
        );

        $this->assertSame(
            $this->resultSet,
            $result
        );

        $this->assertSame(
            $initRows,
            $this->resultSet->getRows()
        );

        $this->assertInstanceOf(
            ArrayCollection::class,
            $this->resultSet->getRows()
        );

        $this->assertSame(
            $rows,
            $this->resultSet->getRows()->asArray()
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