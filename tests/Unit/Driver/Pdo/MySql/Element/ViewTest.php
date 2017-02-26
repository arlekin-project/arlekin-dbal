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

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ViewTest extends BaseTest
{
    /**
     * @covers View::__construct
     */
    public function testConstruct()
    {
        $view = new View('foo', 'SELECT 1');

        $this->assertAttributeSame('foo', 'name', $view);
        $this->assertAttributeSame('SELECT 1', 'definition', $view);
    }

    /**
     * @covers View::getName
     */
    public function testGetName()
    {
        $view = new View('foo', 'SELECT 1');

        $this->assertSame('foo', $view->getName());
    }

    /**
     * @covers View::getDefinition
     */
    public function testGetDefinition()
    {
        $view = new View('foo', 'SELECT 1');

        $this->assertSame('SELECT 1', $view->getDefinition());
    }
}
