<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Tests;

use Arlekin\DatabaseAbstractionLayer\DatabaseAbstractionLayerCompilerPass;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DatabaseAbstractionLayerCompilerPassTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\DatabaseAbstractionLayer\DatabaseAbstractionLayerCompilerPass::process
     */
    public function testProcess()
    {
        $container = new ContainerBuilder();

        $compilerPass = new DatabaseAbstractionLayerCompilerPass();

        $compilerPass->process(
            $container
        );

        $this->assertSame(
            array(),
            $container->getParameter(
                'dbal.driver_ids_by_driver_name'
            )
        );
    }
}
