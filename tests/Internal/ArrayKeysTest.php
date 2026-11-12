<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Internal;

use Dranzd\Common\Domain\ValueObject\Internal\ArrayKeys;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArrayKeys::class)]
final class ArrayKeysTest extends TestCase
{
    /**
     * Each key of the table converted by hand. These pin the conversion the composite factories have always done.
     *
     * @return array<string, array{array-key, array-key}>
     */
    public static function keyProvider(): array
    {
        return [
            'snake_case' => ['zip_code', 'zipCode'],
            'camelCase is unchanged' => ['zipCode', 'zipCode'],
            'kebab-case' => ['zip-code', 'zipCode'],
            'PascalCase' => ['ZipCode', 'zipCode'],
            'Snake_Pascal' => ['Zip_Code', 'zipCode'],
            'lower word' => ['currency', 'currency'],
            'Capitalised word' => ['Currency', 'currency'],
            'ALLCAPS only loses its first letter case' => ['CURRENCY', 'cURRENCY'],
            'SNAKE_CAPS' => ['ZIP_CODE', 'zIPCODE'],
            'digit inside a word' => ['street1', 'street1'],
            'digit word' => ['street_1', 'street1'],
            'digits only after conversion become an integer key' => ['1_0', 10],
            'leading separator' => ['_a', 'a'],
            'leading underscores' => ['__a', 'a'],
            'trailing separator' => ['a_', 'a'],
            'doubled separator' => ['a__b', 'aB'],
            'only separators' => ['--', ''],
            'mixed separators' => ['a-b_c d', 'aBCD'],
            'space is a word break kept out' => ['a b', 'aB'],
            'tab is a word start' => ["a\tb", "a\tB"],
            'empty key' => ['', ''],
            'non-ASCII lower case is not capitalised' => ['é_a', 'éA'],
            'non-ASCII upper case first letter is not lowered' => ['Éa', 'Éa'],
            'non-ASCII word' => ['日本_語', '日本語'],
            'invalid UTF-8 is converted like any other bytes' => ["\xff_a", "\xffA"],
            'invalid UTF-8 truncated sequence' => ["a_\xc3", "a\xc3"],
            'integer key stays an integer' => [5, 5],
            'zero stays zero' => [0, 0],
            'negative integer key loses its sign' => [-5, 5],
            'negative numeric string loses its sign' => ['-5', 5],
            'numeric string key becomes an integer key' => ['10', 10],
            'numeric string with leading zero stays a string' => ['07', '07'],
            'decimal numeric string stays a string' => ['1.5', '1.5'],
        ];
    }

    #[DataProvider('keyProvider')]
    public function testToCamelCaseConvertsKey(int|string $key, int|string $expected): void
    {
        self::assertSame([$expected => 'v'], ArrayKeys::toCamelCase([$key => 'v']));
    }

    public function testEmptyArrayStaysEmpty(): void
    {
        self::assertSame([], ArrayKeys::toCamelCase([]));
    }

    public function testValuesAndOrderAreKept(): void
    {
        self::assertSame(
            ['zipCode' => 1, 'amount' => null, 'a' => [1, 2], 0 => 'x'],
            ArrayKeys::toCamelCase(['zip_code' => 1, 'Amount' => null, 'a' => [1, 2], 0 => 'x'])
        );
    }

    public function testConversionDoesNotRecurseIntoValues(): void
    {
        self::assertSame(
            ['outerKey' => ['inner_key' => 1]],
            ArrayKeys::toCamelCase(['outer_key' => ['inner_key' => 1]])
        );
    }

    public function testCollidingKeysLetTheLaterOneWin(): void
    {
        self::assertSame(['zipCode' => '2000'], ArrayKeys::toCamelCase(['zip_code' => '1000', 'zipCode' => '2000']));
        self::assertSame(['zipCode' => '1000'], ArrayKeys::toCamelCase(['zipCode' => '2000', 'zip_code' => '1000']));
        self::assertSame(
            ['currency' => 'EUR', 'amount' => 1],
            ArrayKeys::toCamelCase(['currency' => 'USD', 'amount' => 1, 'Currency' => 'EUR'])
        );
    }

    public function testCollidingKeysKeepThePositionOfTheFirst(): void
    {
        self::assertSame(
            ['a', 'b'],
            \array_keys(ArrayKeys::toCamelCase(['a' => 1, 'b' => 2, 'A' => 3]))
        );
    }

    public function testIntegerKeyCollidingWithAConvertedKeyLetsTheLaterOneWin(): void
    {
        self::assertSame([10 => 'b'], ArrayKeys::toCamelCase([10 => 'a', '1_0' => 'b']));
    }

    /**
     * The conversion of dranzd/common-utils 1.0, which the composite factories used before they stopped depending
     * on it: `StringUtil::toCamelCase()` applied to every key with `array_map()`, then `array_combine()`.
     *
     * @param array<array-key, mixed> $array
     * @return array<array-key, mixed>
     */
    private static function previousConversion(array $array): array
    {
        $toCamelCase = static function (string $string): string {
            $string = \str_replace(['-', '_'], ' ', $string);
            $string = \ucwords($string);
            $string = \str_replace(' ', '', $string);

            return \lcfirst($string);
        };

        return \array_combine(\array_map($toCamelCase, \array_keys($array)), \array_values($array));
    }

    /**
     * @return array<string, array{array<array-key, mixed>}>
     */
    public static function arrayProvider(): array
    {
        $keys = [
            'zip_code', 'zipCode', 'ZipCode', 'zip-code', 'Zip_Code', 'ZIP_CODE', 'amount', 'Amount', 'AMOUNT',
            'currency', 'Currency', 'street1', 'street_1', '1_0', '10', '07', '1.5', '-5', '0', '_', '__a', 'a__b',
            'a_', '_a', '-a-', 'a b', ' a', "a\tb", "a\nb", '', ' ', 'é_a', 'ÉCOLE', '日本_語', "\xff", "\xff_a",
            "\xc3\x28_b", 'UserID', 'user_ID', 'HTTPServer', '--', 'this', 'a.b', 0, 1, -1, PHP_INT_MAX, PHP_INT_MIN,
        ];

        $cases = ['empty' => [[]], 'all keys at once' => [\array_fill_keys($keys, 'v')]];
        foreach ($keys as $first) {
            foreach ($keys as $second) {
                $name = \sprintf(
                    '%s then %s',
                    \json_encode($first, JSON_INVALID_UTF8_SUBSTITUTE),
                    \json_encode($second, JSON_INVALID_UTF8_SUBSTITUTE)
                );
                $cases[$name] = [[$first => 'first', $second => 'second']];
            }
        }

        return $cases;
    }

    /**
     * @param array<array-key, mixed> $array
     */
    #[DataProvider('arrayProvider')]
    public function testToCamelCaseMatchesThePreviousConversion(array $array): void
    {
        $actual = ArrayKeys::toCamelCase($array);

        self::assertSame(self::previousConversion($array), $actual);
        self::assertSame(\array_keys(self::previousConversion($array)), \array_keys($actual));
    }
}
