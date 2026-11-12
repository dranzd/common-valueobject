<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Money;

use Dranzd\Common\Domain\ValueObject\Money\CurrencyCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(CurrencyCode::class)]
final class CurrencyCodeTest extends TestCase
{
    private const EXPECTED = [
        'USD', 'EUR', 'GBP', 'JPY', 'CNY', 'PHP', 'SGD', 'AUD', 'CAD', 'CHF', 'HKD', 'NZD', 'KRW', 'THB',
        'INR', 'MYR', 'IDR', 'VND', 'TWD', 'BRL', 'MXN', 'ZAR', 'SEK', 'NOK', 'DKK', 'AED', 'SAR',
    ];

    public function testCaseListIsExactlyTheDocumentedOne(): void
    {
        $values = [];
        foreach (CurrencyCode::cases() as $code) {
            $values[] = $code->value;
        }

        self::assertSame(self::EXPECTED, $values);
    }

    public function testCaseNamesEqualTheirValues(): void
    {
        foreach (CurrencyCode::cases() as $code) {
            self::assertSame($code->name, $code->value);
        }
    }

    /**
     * @return array<string, array{string, string|null}>
     */
    public static function lookupProvider(): array
    {
        return [
            'exact' => ['USD', 'USD'],
            'another exact' => ['EUR', 'EUR'],
            'unknown' => ['XYZ', null],
            'lower case is not accepted' => ['usd', null],
        ];
    }

    #[DataProvider('lookupProvider')]
    public function testTryFrom(string $input, ?string $expected): void
    {
        self::assertSame($expected, CurrencyCode::tryFrom($input)?->value);
    }

    public function testFromReturnsTheCaseOfEveryValue(): void
    {
        foreach (CurrencyCode::cases() as $code) {
            self::assertSame($code, CurrencyCode::from($code->value));
        }
    }

    public function testFromUnknownCodeThrowsValueError(): void
    {
        $this->expectException(\ValueError::class);

        CurrencyCode::from('XYZ');
    }
}
