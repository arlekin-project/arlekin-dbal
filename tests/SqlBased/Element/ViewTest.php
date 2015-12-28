<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Tests\SqlBased\Element;

use Arlekin\DatabaseAbstractionLayer\SqlBased\Element\View;
use Arlekin\Common\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\View::getName
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\View::setName
     */
    public function testGetAndSetName()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewView(),
            'name',
            uniqid('test_name_', true)
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\View::getDefinition
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\View::setDefinition
     */
    public function testGetAndSetDefinition()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewView(),
            'definition',
            uniqid('test_definitin_', true)
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\SqlBased\Element\View::toArray
     */
    public function testToArray()
    {
        $view = $this->createBaseNewView();

        $name = uniqid('test_name_', true);
        $definition = uniqid('test_definition_', true);

        $view->setName(
            $name
        )->setDefinition(
            $definition
        );

        $this->assertSame(
            [
                'name' => $name,
                'definition' => $definition,
            ],
            $view->toArray()
        );
    }

    /**
     * @return View
     */
    protected function createBaseNewView()
    {
        return $this->getMockForAbstractClass(
            View::class
        );
    }
}