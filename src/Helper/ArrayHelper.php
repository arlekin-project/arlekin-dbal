<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Helper;

/**
 * To help managing arrays.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class ArrayHelper
{
    /**
     * Returns a copy of the first array
     * less the elements that were both in the first and the second array.
     *
     * @param $array1
     * @param $array2
     *
     * @return array
     */
    public static function arrayDiffRecursive (array $array1, array $array2)
    {
        $diff = [];

        $rightWithIntegerIndex = [];

        foreach ($array2 as $key => $value) {
            if (is_numeric($key)) {
                $rightWithIntegerIndex[] = $value;
            }
        }

        foreach ($array1 as $key => $value) {
            if (is_numeric($key) && !in_array($value, $rightWithIntegerIndex)) {
                if (is_array($value)) {
                    $nestedArrayDiff = self::arrayDiffRecursive(
                        $value,
                        $array2[$key]
                    );

                    if (!empty($nestedArrayDiff)) {
                        $diff[] = $nestedArrayDiff;
                    }
                } else {
                    $diff[] = $value;
                }
            }
        }

        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2) && !is_numeric($key) && $array2[$key] !== $array1[$key]) {
                if (is_array($value)) {
                    $nestedArrayDiff = self::arrayDiffRecursive(
                        $value,
                        $array2[$key]
                    );

                    if (!empty($nestedArrayDiff)) {
                        $diff[$key] = $nestedArrayDiff;
                    }
                } else {
                    $diff[$key] = $array1[$key];
                }
            }
        }

        return $diff;
    }

    /**
     * Whether the two given arrays are the same.
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public static function arraysAreSameRecursive(array $array1, array $array2)
    {
        $diff = self::arrayDiffRecursive(
            $array1,
            $array2
        );

        $diff1 = self::arrayDiffRecursive(
            $array2,
            $array1
        );

        return empty($diff) && empty($diff1);
    }
}
