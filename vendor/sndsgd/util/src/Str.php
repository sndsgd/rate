<?php

namespace sndsgd;

/**
 * String utility methods
 */
class Str
{
    /**
     * Determine if a string begins with another
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $caseSensitive
     * @return bool
     */
    public static function beginsWith(
        string $haystack,
        string $needle,
        bool $caseSensitive = false
    ): bool
    {
        $length = strlen($needle);
        return ($caseSensitive === false)
            ? strncasecmp($haystack, $needle, $length) === 0
            : strncmp($haystack, $needle, $length) === 0;
    }

    /**
     * Determine if a string ends with another
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $caseSensitive
     * @return bool
     */
    public static function endsWith(
        string $haystack,
        string $needle,
        bool $caseSensitive = false
    ): bool
    {
        $test = substr($haystack, -strlen($needle));
        return ($caseSensitive === false)
            ? strcasecmp($test, $needle) === 0
            : strcmp($test, $needle) === 0;
    }

    /**
     * Get the substring that occurs before another
     *
     * @param string $haystack The string to search within
     * @param string $needle The end of the string to return
     * @return string
     */
    public static function before(string $haystack, string $needle): string
    {
        $pos = strpos($haystack, $needle);
        return ($pos !== false)
            ? substr($haystack, 0, $pos)
            : $haystack;
    }

    /**
     * Get the substring that occurs after another
     *
     * @param string $haystack The string to search within
     * @param string $needle The string immediately before the desired result
     * @return string
     */
    public static function after(string $haystack, string $needle): string
    {
        $pos = strpos($haystack, $needle);
        return ($pos !== false)
            ? substr($haystack, $pos + strlen($needle))
            : $haystack;
    }

    /**
     * Get a random string
     *
     * @param int $length The length of the resulting string
     * @return string
     */
    public static function random(int $length): string
    {
        $byteLength = ($length < 10) ? 10 : $length;
        $chars = base64_encode(random_bytes($byteLength));
        $chars = str_replace(["+", "/", "="], "", $chars);
        return substr($chars, 0, $length);
    }

    /**
     * Convert a string to a number
     *
     * @param string $str
     * @return int|float
     */
    public static function toNumber($str)
    {
        if (is_string($str)) {
            $str = preg_replace("~[^0-9-.]~", "", $str);
        }
        return (strpos($str, ".") === false)
            ? intval($str)
            : floatval($str);
    }

    /**
     * Convert boolean strings to real booleans
     *
     * @param string|number $str
     * @return bool|null
     */
    public static function toBoolean($str)
    {
        if (!is_string($str)) {
            $str = strval($str);
        }

        $str = strtolower($str);
        $values = [
            "true" => true,
            "false" => false,
            "1" => true,
            "0" => false,
            "on" => true,
            "off" => false,
            "" => false
        ];
        return (array_key_exists($str, $values))
            ? $values[$str]
            : null;
    }

    /**
     * Convert a string to camelCase
     *
     * @param string $str
     * @return string
     */
    public static function toCamelCase(string $str): string
    {
        $str = trim($str);
        $fn = function($arg) {
            list($match, $char) = $arg;
            $ret = str_replace($char, "", $match);
            return strtoupper($ret);
        };
        return preg_replace_callback("~( |_|-){1,}[A-Za-z]~", $fn, $str);
    }

    /**
     * Convert a string to snake_case
     *
     * @param string $str
     * @param bool $uppercase
     * @return string
     */
    public static function toSnakeCase(
        string $str,
        bool $uppercase = false
    ): string
    {
        $str = trim($str);
        $str = preg_replace("~[^a-z0-9]+~i", "_", $str);

        /**
         * @param array<string> $arg
         *  [0] the match
         *  [1] the character
         */
        $fn = function($arg) {
            $match = $arg[0];
            return $match[0]."_".$match[1];
        };
        $ret = preg_replace_callback("~([a-z])[A-Z]~", $fn, $str);
        return ($uppercase) ? strtoupper($ret) : strtolower($ret);
    }

    /**
     * Remove all tabs that occur immediately after a newline
     *
     * @param string $str
     * @return string
     */
    public static function stripPostNewlineTabs(string $str): string
    {
        $regex = "~".PHP_EOL."[\t]+~";
        return preg_replace($regex, PHP_EOL, $str);
    }

    /**
     * Remove empty lines
     *
     * @param string $str
     * @return string
     */
    public static function stripEmptyLines(string $str): string
    {
        $regex = "~(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+~";
        return preg_replace($regex, PHP_EOL, $str);
    }

    /**
     * Summarize a string to a given max length
     *
     * @param string $str The value to summarize
     * @param int $maxLength The max number of characters to return
     * @param string $ellipsis A string to append to the result
     * @param string $needle If provided, attempt to split on this string
     */
    public static function summarize(
        string $str,
        int $maxLength,
        string $ellipsis = "...",
        string $needle = ""
    ): string
    {
        $length = mb_strlen($str);
        if ($length <= $maxLength) {
            return $str;
        }

        $endPos = $maxPos = $maxLength - mb_strlen($ellipsis);
        if ($needle !== "") {
            $offset = $maxPos - $length;
            $endPos = mb_strrpos($str, $needle, $offset);
            if ($endPos === false) {
                $endPos = $maxPos;
            }
        }

        return mb_substr($str, 0, $endPos).$ellipsis;
    }

    /**
     * Handle string replacements given an array of find => replace values
     *
     * This was added to keep sndsgd\field\Error::getMessage() DRY
     * @param string $str The string to replace within
     * @param array<string,string> $values
     * @return string
     */
    public static function replace(string $str, array $values): string
    {
        return str_replace(array_keys($values), array_values($values), $str);
    }
}
