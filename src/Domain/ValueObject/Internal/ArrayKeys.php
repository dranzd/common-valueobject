<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Internal;

/**
 * Array key conversion used by the `fromArray()` factories of the composite value objects.
 *
 * @internal Not part of the public API; it may change or disappear in any release.
 */
final class ArrayKeys
{
    /**
     * Convert every key to camelCase, without recursing into values.
     *
     * Each key is cast to a string, `-` and `_` become word breaks, every word is capitalised, the breaks are removed
     * and the first character is lower-cased: `zip_code`, `zip-code` and `Zip_Code` all become `zipCode`, while
     * `Currency` becomes `currency` and `ZIPCODE` becomes `zIPCODE`. The conversion is byte-based and never throws,
     * so a key that is not valid UTF-8 is converted like any other. When two keys convert to the same key, the later
     * one wins. A non-negative integer key keeps its value (`0` stays `0`). A negative one loses its sign (`-5`
     * becomes `5`), because `-` is a separator. A result that is a decimal integer string (`'1_0'` becomes `10`)
     * becomes an integer key, as for any PHP array key.
     *
     * @param array<array-key, mixed> $array
     * @return array<array-key, mixed>
     */
    public static function toCamelCase(array $array): array
    {
        $converted = [];
        foreach ($array as $key => $value) {
            $words = \ucwords(\str_replace(['-', '_'], ' ', (string) $key));
            $converted[\lcfirst(\str_replace(' ', '', $words))] = $value;
        }

        return $converted;
    }
}
