<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Helper;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class CommonTestHelper
{
    public static function assertExceptionThrown(
        callable $callback,
        $expectedExceptionClass,
        $expectedExceptionMessage
    ) {
        $exceptionThrown = false;

        try {
            $callback();
        } catch (\Exception $ex) {
            Assert::assertInstanceOf(
                $expectedExceptionClass,
                $ex,
                sprintf(
                    'Failed asserting that exception of class "%s" was thrown. Got exception of class "%s".',
                    $expectedExceptionClass,
                    get_class($ex)
                )
            );

            Assert::assertEquals(
                $expectedExceptionMessage,
                $ex->getMessage()
            );

            $exceptionThrown = true;
        }

        Assert::assertTrue(
            $exceptionThrown,
            sprintf(
                'Failed asserting that exception of class "%s" was thrown.',
                $expectedExceptionClass
            )
        );
    }

    /**
     * Camelizes a given string.
     *
     * @param  string $string Some string
     *
     * @return string The camelized version of the string
     */
    public static function camelize($string)
    {
        //Copied from Symfony\Component\PropertyAccess\PropertyAccessor.
        return preg_replace_callback(
            '/(^|_|\.)+(.)/',
            function ($match) {
                return (
                    '.' === $match[1] ? '_' : ''
                ).strtoupper(
                    $match[2]
                );
            },
            $string
        );
    }

    /**
     * @param string $property
     *
     * @return string
     */
    public static function getSetterNameForProperty($property)
    {
        return 'set'.self::camelize($property);
    }

    /**
     * @param string $property
     * @param string $propertySingular
     *
     * @return string
     */
    public static function getAdderNameForProperty($property, $propertySingular = null)
    {
        if ($propertySingular === null) {
            $propertyNameToUse = substr_replace($property, '', -1);
        } else {
            $propertyNameToUse = $propertySingular;
        }

        return 'add'.self::camelize($propertyNameToUse);
    }

    /**
     * @param string $property
     *
     * @return string
     */
    public static function getCollectionAdderNameForProperty($property)
    {
        return 'add'.self::camelize($property);
    }

    /**
     * @param string $property
     * @return string
     */
    public static function getGetterNameForProperty($property)
    {
        return 'get'.self::camelize($property);
    }

    /**
     * @param string $property
     *
     * @return string
     */
    public static function getIsserNameForProperty($property)
    {
        return 'is'.self::camelize($property);
    }

    /**
     * @param mixed $classNameOrObject
     * @param string $methodName
     *
     * @return \ReflectionMethod
     */
    public static function getMethod($classNameOrObject, $methodName)
    {
        $class = new \ReflectionClass($classNameOrObject);

        $method = $class->getMethod($methodName);

        return $method;
    }

    /**
     * @param mixed $classNameOrObject
     * @param string $property
     *
     * @return \ReflectionMethod
     */
    public static function getGetterForProperty($classNameOrObject, $property)
    {
        $getterName = self::getGetterNameForProperty($property);

        $getter = self::getMethod($classNameOrObject, $getterName);

        return $getter;
    }

    /**
     * @param mixed $classNameOrObject
     * @param string $property
     *
     * @return \ReflectionMethod
     */
    public static function getSetterForProperty($classNameOrObject, $property)
    {
        $setterName = self::getSetterNameForProperty($property);

        $setter = self::getMethod($classNameOrObject, $setterName);

        return $setter;
    }

    /**
     * @param mixed $classNameOrObject
     * @param string $property
     *
     * @return \ReflectionMethod
     */
    public static function getIsserForProperty($classNameOrObject, $property)
    {
        $isserName = self::getIsserNameForProperty($property);

        $setter = self::getMethod($classNameOrObject, $isserName);

        return $setter;
    }

    /**
     * @param mixed $classNameOrObject
     * @param string $property
     * @param string $propertySingular
     *
     * @return \ReflectionMethod
     */
    public static function getAdderForProperty($classNameOrObject, $property, $propertySingular = null)
    {
        $setterName = self::getAdderNameForProperty($property, $propertySingular);

        $setter = self::getMethod($classNameOrObject, $setterName);

        return $setter;
    }

    /**
     * @param mixed $classNameOrObject
     * @param string $property
     *
     * @return \ReflectionMethod
     */
    public static function getCollectionAdderForProperty($classNameOrObject, $property)
    {
        $setterName = self::getCollectionAdderNameForProperty($property);

        $setter = self::getMethod($classNameOrObject, $setterName);

        return $setter;
    }

    /**
     * @param TestCase $testCase
     * @param object $object
     * @param string $property
     * @param mixed $value
     */
    public static function testBasicGetAndSetForProperty(
        TestCase $testCase,
        $object,
        $property,
        $value
    ) {
        $getter = self::getGetterForProperty($object, $property);

        $setter = self::getSetterForProperty($object, $property);

        $retrievedObjectForFluidInterface = $setter->invoke($object, $value);

        $testCase->assertSame(
            $retrievedObjectForFluidInterface,
            $object,
            'Failed asserting that retrieved value from setter'
            .' and object argument are the sames,'
            .' is required fluid interface implemented?'
        );

        $retrieved = $getter->invoke($object);

        $testCase->assertSame($value, $retrieved);
    }

    /**
     * @param TestCase $testCase
     * @param object $object
     * @param string $property
     * @param mixed $value
     */
    public static function testBasicIsAndSetForProperty(
        TestCase $testCase,
        $object,
        $property,
        $value
    ) {
        $isser = self::getIsserForProperty($object, $property);

        $setter = self::getSetterForProperty($object, $property);

        $retrievedObjectForFluidInterface = $setter->invoke($object, $value);

        $testCase->assertSame(
            $retrievedObjectForFluidInterface,
            $object,
            'Failed asserting that retrieved value from setter'
            .' and object argument are the sames,'
            .' is required fluid interface implemented?'
        );

        $retrieved = $isser->invoke($object);

        $testCase->assertSame($value, $retrieved);
    }

    /**
     * @param TestCase $testCase
     * @param object $object
     * @param string $property
     * @param mixed $value
     * @param string $propertySingular
     */
    public static function testBasicAddForProperty(
        TestCase $testCase,
        $object,
        $property,
        $value,
        $propertySingular = null
    ) {
        $getter = self::getGetterForProperty($object, $property);

        $setter = self::getAdderForProperty($object, $property, $propertySingular);

        $retrievedObjectForFluidInterface = $setter->invoke($object, $value);

        $testCase->assertSame(
            $retrievedObjectForFluidInterface,
            $object,
            'Failed asserting that retrieved value from setter'
            .' and object argument are the sames,'
            .' is required fluid interface implemented?'
        );

        $retrieved = $getter->invoke($object);

        $testCase->assertTrue(
            $retrieved->hasElement($value)
        );
    }

    /**
     * @param TestCase $testCase
     * @param object $object
     * @param string $property
     * @param array $value
     */
    public static function testBasicGetAndSetCollectionForProperty(
        TestCase $testCase,
        $object,
        $property,
        $value
    ) {
        $getter = self::getGetterForProperty($object, $property);

        $setter = self::getSetterForProperty($object, $property);

        $retrievedObjectForFluidInterface = $setter->invoke($object, $value);

        $testCase->assertSame(
            $retrievedObjectForFluidInterface,
            $object,
            'Failed asserting that retrieved value from setter'
            .' and object argument are the sames,'
            .' is required fluid interface implemented?'
        );

        $retrieved = $getter->invoke($object);

        $testCase->assertSame($value, $retrieved);
    }

    /**
     * @param TestCase $testCase
     * @param object $object
     * @param string $property
     * @param mixed $values
     */
    public static function testBasicAddCollectionForProperty(
        TestCase $testCase,
        $object,
        $property,
        array $values
    ) {
        $getter = self::getGetterForProperty($object, $property);

        $setter = self::getCollectionAdderForProperty($object, $property);

        $retrievedObjectForFluidInterface = $setter->invoke($object, $values);

        $testCase->assertSame(
            $retrievedObjectForFluidInterface,
            $object,
            'Failed asserting that retrieved value from setter'
            .' and object argument are the sames,'
            .' is required fluid interface implemented?'
        );

        $retrieved = $getter->invoke($object);

        foreach ($values as $value) {
            $testCase->assertTrue(
                $retrieved->hasElement($value)
            );
        }
    }

    /**
     * @param object $instance
     * @param string $methodName
     *
     * @return \ReflectionMethod
     */
    public static function getMethodFromInstance($instance, $methodName)
    {
        $reflectionClass = new \ReflectionClass($instance);

        $method = $reflectionClass->getMethod($methodName);

        return $method;
    }

    /**
     * @param string $class
     * @param string $methodName
     *
     * @return \ReflectionMethod
     */
    public static function getMethodFromClass($class, $methodName)
    {
        $reflectionClass = new \ReflectionClass($class);

        $method = $reflectionClass->getMethod($methodName);

        return $method;
    }

    public static function objectToArray($objects)
    {
        if (is_array($objects) || $objects instanceof \Traversable) {
            $arr = [];

            foreach ($objects as $key => $object) {
                $arr[$key] = self::objectToArray($object);
            }
        } else {
            if (is_object($objects)) {
                $reflectionClass = new \ReflectionClass($objects);

                $properties = $reflectionClass->getProperties();

                $arr = [];

                foreach ($properties as $property) {
                    /* @var $property \ReflectionProperty */

                    $property->setAccessible(true);

                    $value = $property->getValue($objects);

                    $name = $property->getName();

                    if (is_object($value)) {
                        $arr[$name] = self::objectToArray($value);
                    } else {
                        $arr[$name] = $value;
                    }
                    
                    $property->setAccessible(false);
                }
            } else {
                $arr = $objects;
            }
        }

        return $arr;
    }
}
