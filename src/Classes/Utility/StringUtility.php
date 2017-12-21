<?php

namespace BIT\FUM\Utility;

/**
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
}
