<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Tests\SqlBased;

use Arlekin\Core\Collection\ArrayCollection;
use Arlekin\DatabaseAbstractionLayer\SqlBased\Query;
use Arlekin\Core\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class QueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Query::__construct
     */
    public function testConstruct()
    {
        $query = new Query();

        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'parameters',
            $query
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Query::getSql
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Query::setSql
     */
    public function testGetSqlAndSetSql()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->query,
            'sql',
            'zqsd'
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Query::getParameters
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Query::setParameters
     */
    public function testGetSqlAndSetParameters()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->query,
            'parameters',
            array(
                'test' => 'test'
            )
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Query::setParameter
     */
    public function testSetParameter()
    {
        $result = $this->query->setParameter(
            'test',
            42
        );
        $this->assertSame(
            $this->query,
            $result
        );
        $this->assertSame(
            $this->query->getParameters()['test'],
            42
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->query = new Query();
    }
}