<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Tests\Helper;

use Arlekin\DatabaseAbstractionLayer\DriverInterface;
use Arlekin\DatabaseAbstractionLayer\Helper\ExtensionHelper;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\DatabaseAbstractionLayer\Helper\ExtensionHelper::getDriverIdsByDriverNameFromTaggedServiceIds
     */
    public function testGetDriverIdsByDriverNameFromTaggedServiceIds()
    {
        $driverMock = $this->getMock(
            DriverInterface::class
        );

        $driverMock->method(
            'getName'
        )->will(
            $this->returnValue(
                'test'
            )
        );

        $containerMock = $this->getMock(
            ContainerBuilder::class,
            array(
                'findTaggedServiceIds',
                'get',
            )
        );

        $containerMock->method(
            'findTaggedServiceIds'
        )->will(
            $this->returnCallback(
                function (
                    $id
                ) {
                    if ($id === 'dbal.driver') {
                        return array(
                            'dbal.driver.test' => array(),
                        );
                    }
                }
            )
        );

        $containerMock->method(
            'get'
        )->will(
            $this->returnCallback(
                function (
                    $id
                ) use (
                    $driverMock
                ) {
                    if ($id === 'dbal.driver.test') {
                        return $driverMock;
                    }
                }
            )
        );

        $this->assertSame(
            array(
                'test' => 'dbal.driver.test'
            ),
            ExtensionHelper::getDriverIdsByDriverNameFromTaggedServiceIds(
                $containerMock
            )
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\Helper\ExtensionHelper::getParametersByDatabaseConnectionName
     */
    public function testGetParametersByDatabaseConnectionNameNoConnection()
    {
        $this->assertSame(
            array(),
            ExtensionHelper::getParametersByDatabaseConnectionName(
                array()
            )
        );
    }

    /**
     * @covers Arlekin\DatabaseAbstractionLayer\Helper\ExtensionHelper::getParametersByDatabaseConnectionName
     */
    public function testGetParametersByDatabaseConnectionName()
    {
        $this->assertSame(
            array(
                'test_connection_name' => array(
                    'name' => 'test_connection_name',
                    'value' => 42
                )
            ),
            ExtensionHelper::getParametersByDatabaseConnectionName(
                array(
                    'connections' => array(
                        array(
                            'name' => 'test_connection_name',
                            'value' => 42
                        )
                    )
                )
            )
        );
    }
}
