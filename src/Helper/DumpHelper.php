<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Helper;

use Calam\Dbal\Helper\Dump\RawStringValue;

class DumpHelper
{
    public static function dumpValue($value)
    {
        if ($value instanceof RawStringValue) {
            $dumped = (string)$value;
        } elseif ($value === null) {
            $dumped = 'null';
        } elseif (is_string($value)) {
            $dumped = sprintf(
                '\'%s\'',
                $value
            );
        } elseif (is_bool($value)) {
            if ($value === true) {
                $dumped = 'true';
            } else {
                $dumped = 'false';
            }
        } elseif (is_array($value)) {
            $dumped = self::dumpArray(
                $value
            );
        } elseif (is_resource($value) || is_object($value)) {
            throw new \Calam\Dbal\Exception\DbalException(
                sprintf(
                    'Given "%s" cannot be dumped.',
                    gettype($value)
                )
            );
        } else {
            $dumped = (string)$value;
        }

        return $dumped;
    }

    /**
     * @param array $array
     * @param integer $indentRow
     */
    public static function dumpArray(
        array $array,
        $indent = '    ',
        $indentRow = 1
    ) {
        if (empty($array)) {
            $val = '[]';
        } else {
            $val = '[' . PHP_EOL;

            foreach ($array as $key => $value) {
                for ($i = 0; $i < $indentRow; $i += 1) {
                    $val .= $indent;
                }
                if (is_array($value)) {
                    $dumpedValue = self::dumpArray(
                        $value,
                        $indent,
                        $indentRow + 1
                    );
                } else {
                    $dumpedValue = self::dumpValue(
                        $value
                    );
                }

                $val .= sprintf(
                    '%s => %s,',
                    self::dumpValue(
                        $key
                    ),
                    $dumpedValue
                ) .PHP_EOL;
            }

            for ($i = 0; $i < $indentRow - 1; $i += 1) {
                $val .= $indent;
            }

            $val .= ']';
        }

        return $val;
    }
}
