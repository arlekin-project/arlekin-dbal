<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Tests\Helper;

use Arlekin\Dbal\Helper\ObjectHelper;
use Arlekin\Dbal\Tests\Fixtures\DummyObject;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ObjectHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlekin\Dbal\Helper\ObjectHelper::forcePropertyAccessible
     */
    public function testForcePropertyAccessible()
    {
        $dummyObject = new DummyObject();

        $reflectionClass = new \ReflectionClass($dummyObject);

        $reflectionProperty = $reflectionClass->getProperty('testProperty');

        $exceptionThrown = false;

        try {
            $reflectionProperty->setValue($dummyObject, 42);
        } catch (\ReflectionException $ex) {
            $this->assertSame(
                'Cannot access non-public member Arlekin\Dbal\Tests\Fixtures\DummyObject::testProperty',
                $ex->getMessage()
            );

            $exceptionThrown = true;
        }

        $this->assertTrue(
            $exceptionThrown
        );
        $this->assertNull(
            $dummyObject->getTestProperty()
        );

        ObjectHelper::forcePropertyAccessible($reflectionProperty);

        $reflectionProperty->setValue($dummyObject, 42);

        $this->assertSame(
            42,
            $dummyObject->getTestProperty()
        );
    }

    /**
     * @covers Arlekin\Dbal\Helper\ObjectHelper::resetPropertyAccessibility
     */
    public function testResetPropertyAccessibility()
    {
        $dummyObject = new DummyObject();

        $reflectionClass = new \ReflectionClass($dummyObject);

        $reflectionProperty = $reflectionClass->getProperty('testProperty');

        ObjectHelper::forcePropertyAccessible($reflectionProperty);
        ObjectHelper::resetPropertyAccessibility($reflectionProperty);

        $exceptionThrown = false;

        try {
            $reflectionProperty->setValue($dummyObject, 42);
        } catch (\ReflectionException $ex) {
            $this->assertSame(
                'Cannot access non-public member Arlekin\Dbal\Tests\Fixtures\DummyObject::testProperty',
                $ex->getMessage()
            );

            $exceptionThrown = true;
        }

        $this->assertTrue(
            $exceptionThrown
        );
    }

    /**
     * @covers Arlekin\Dbal\Helper\ObjectHelper::forceSetProperty
     */
    public function testForceSetProperty()
    {
        $dummyObject = new DummyObject();

        $this->assertNull(
            $dummyObject->getTestProperty()
        );

        ObjectHelper::forceSetProperty('testProperty', $dummyObject, 42);

        $this->assertSame(
            42,
            $dummyObject->getTestProperty()
        );
    }

    /**
     * @covers Arlekin\Dbal\Helper\ObjectHelper::forceSetAlreadyAccessibleProperty
     */
    public function testForceSetAlreadyAccessibleProperty()
    {
        $dummyObject = new DummyObject();

        $reflectionClass = new \ReflectionClass($dummyObject);

        $reflectionProperty = $reflectionClass->getProperty('testProperty');

        ObjectHelper::forcePropertyAccessible($reflectionProperty);

        $this->assertNull(
            $dummyObject->getTestProperty()
        );

        ObjectHelper::forceSetAlreadyAccessibleProperty($reflectionProperty, $dummyObject, 42);

        $this->assertSame(
            42,
            $dummyObject->getTestProperty()
        );
    }
}
