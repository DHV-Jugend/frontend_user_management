<?php

namespace BIT\FUM\Utility;

/**
 * Utility functions for string manipulation, mostly copied from TYPO3 StringUtility.
 * @see https://github.com/TYPO3/TYPO3.CMS/blob/6708d691034633cf02b1a7e32d79072f07339db7/typo3/sysext/core/Classes/Utility/StringUtility.php
 *
 * @author Christoph Bessei
 */
class StringUtility
{
    public static function trimExplode(string $delimiter = ',', string $value, bool $removeEmptyValues = true): array
    {
        $values = array_map('trim', explode($delimiter, $value));
        if ($removeEmptyValues) {
            $values = array_filter($values);
        }

        if (!is_array($values)) {
            $values = [];
        }

        return $values;
    }

    /**
     * Returns TRUE if $haystack begins with $needle.
     * The input string is not trimmed before and search is done case sensitive.
     *
     * @param string $haystack Full string to check
     * @param string $needle Reference string which must be found as the "first part" of the full string
     * @throws \InvalidArgumentException
     * @return bool TRUE if $needle was found to be equal to the first part of $haystack
     */
    public static function beginsWith($haystack, $needle)
    {
        // Sanitize $haystack and $needle
        if (is_object($haystack) || $haystack === null || (string)$haystack != $haystack) {
            throw new \InvalidArgumentException(
                '$haystack can not be interpreted as string',
                1347135546
            );
        }
        if (is_object($needle) || (string)$needle != $needle || strlen($needle) < 1) {
            throw new \InvalidArgumentException(
                '$needle can not be interpreted as string or has zero length',
                1347135547
            );
        }
        $haystack = (string)$haystack;
        $needle = (string)$needle;
        return $needle !== '' && strpos($haystack, $needle) === 0;
    }

    /**
     * Returns TRUE if $haystack ends with $needle.
     * The input string is not trimmed before and search is done case sensitive.
     *
     * @param string $haystack Full string to check
     * @param string $needle Reference string which must be found as the "last part" of the full string
     * @throws \InvalidArgumentException
     * @return bool TRUE if $needle was found to be equal to the last part of $haystack
     */
    public static function endsWith($haystack, $needle)
    {
        // Sanitize $haystack and $needle
        if (is_object($haystack) || $haystack === null || (string)$haystack != $haystack) {
            throw new \InvalidArgumentException(
                '$haystack can not be interpreted as string',
                1347135544
            );
        }
        if (is_object($needle) || (string)$needle != $needle || strlen($needle) < 1) {
            throw new \InvalidArgumentException(
                '$needle can not be interpreted as string or has no length',
                1347135545
            );
        }
        $haystackLength = strlen($haystack);
        $needleLength = strlen($needle);
        if (!$haystackLength || $needleLength > $haystackLength) {
            return false;
        }
        $position = strrpos((string)$haystack, (string)$needle);
        return $position !== false && $position === $haystackLength - $needleLength;
    }
}
