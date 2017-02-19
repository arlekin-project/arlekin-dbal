<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\View;
use Calam\Dbal\Tests\BaseTest;
use Calam\Dbal\Tests\Helper\CommonTestHelper;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ViewTest extends BaseTest
{
    /**
     * @covers Calam\Dbal\SqlBased\Element\View::getName
     * @covers Calam\Dbal\SqlBased\Element\View::setName
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
     * @covers Calam\Dbal\SqlBased\Element\View::getDefinition
     * @covers Calam\Dbal\SqlBased\Element\View::setDefinition
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
     * @covers Calam\Dbal\SqlBased\Element\View::toArray
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