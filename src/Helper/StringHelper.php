<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Helper;

/**
 * To help with managing strings.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class StringHelper
{
    /**
     * Whether the haystack string starts with the needle string.
     * Always true if the needle is an empty string.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        return $needle === ''
            || strpos(
                $haystack,
                $needle
            ) === 0;
    }

    /**
     * Whether the haystack string ends with the needle string.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        return $needle === ''
            || substr(
                $haystack,
                -strlen($needle)
            ) === $needle;
    }
}
