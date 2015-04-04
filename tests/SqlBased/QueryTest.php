<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\Tests\SqlBased;

use Arlecchino\Core\Collection\ArrayCollection;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Query;
use Arlecchino\Core\Tests\Helper\CommonTestHelper;
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
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Query::__construct
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
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Query::getSql
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Query::setSql
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
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Query::getParameters
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Query::setParameters
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
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Query::setParameter
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