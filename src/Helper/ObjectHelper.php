<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Helper;

/**
 * To help managing objects.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ObjectHelper
{
    /**
     * Forces accessibility on given property.
     *
     * @param \ReflectionProperty $reflectionProperty
     */
    public static function forcePropertyAccessible(\ReflectionProperty $reflectionProperty)
    {
        $reflectionProperty->setAccessible(true);
    }

    /**
     * Resets accessibility on given property.
     *
     * @param \ReflectionProperty $reflectionProperty
     */
    public static function resetPropertyAccessibility(\ReflectionProperty $reflectionProperty)
    {
        $reflectionProperty->setAccessible(false);
    }

    /**
     * Sets a property,
     * identified by given name,
     * in given object,
     * to given value,
     * regardless its modifiers.
     *
     * @param string $propertyName
     * @param object $object
     * @param mixed $propertyValue
     */
    public static function forceSetProperty($propertyName, $object, $propertyValue)
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperty = $reflectionClass->getProperty($propertyName);

        $reflectionProperty->setAccessible(true);

        $reflectionProperty->setValue($object, $propertyValue);

        $reflectionProperty->setAccessible(false);
    }

    /**
     * Sets a property,
     * identified by given name,
     * in given object,
     * to given value,
     * if it has already been made accessible.
     *
     * @param \ReflectionProperty $reflectionProperty
     * @param object $object
     * @param mixed $propertyValue
     */
    public static function forceSetAlreadyAccessibleProperty(\ReflectionProperty $reflectionProperty, $object, $propertyValue)
    {
        $reflectionProperty->setValue($object, $propertyValue);
    }
}