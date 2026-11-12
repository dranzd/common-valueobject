<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Money;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Money\Currency;
use Dranzd\Common\Domain\ValueObject\Money\CurrencyCode;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CompositeCurrency;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Currency::class)]
#[UsesClass(Literal::class)]
#[UsesClass(CompositeCurrency::class)]
#[UsesTrait(CompareSameValueTrait::class)]
final class CurrencyTest extends TestCase
{
    /**
     * @param class-string<Currency> $class
     */
    private static function make(string $class, string $method, mixed ...$arguments): Currency
    {
        return $class::$method(...$arguments);
    }

    public function testFromStringAcceptsAnyStringWithoutValidation(): void
    {
        self::assertSame('USD', Currency::fromString('USD')->toNative());
        self::assertSame('xyz', Currency::fromString('xyz')->toNative());
        self::assertSame('', Currency::fromString('')->toNative());
        self::assertTrue(Currency::fromString('')->isEmpty());
    }

    public function testFromNativeStaysAvailableFromLiteral(): void
    {
        $currency = Currency::fromNative('EUR');

        self::assertSame('EUR', $currency->toNative());
    }

    public function testToStringIsToNative(): void
    {
        $currency = Currency::fromString('EUR');

        self::assertSame('EUR', $currency->toString());
        self::assertSame($currency->toNative(), $currency->toString());
    }

    public function testFromCode(): void
    {
        self::assertSame('PHP', Currency::fromCode(CurrencyCode::PHP)->toNative());
    }

    public function testMagicFactories(): void
    {
        self::assertSame('USD', Currency::USD()->toNative());
        self::assertSame('EUR', Currency::EUR()->toNative());
        self::assertSame('PHP', Currency::PHP()->toNative());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function everyCodeProvider(): array
    {
        $cases = [];
        foreach (CurrencyCode::cases() as $code) {
            $cases[$code->value] = [$code->value];
        }

        return $cases;
    }

    #[DataProvider('everyCodeProvider')]
    public function testEveryEnumCaseHasAMagicFactory(string $code): void
    {
        self::assertSame($code, Currency::{$code}()->toNative());
        self::assertSame($code, Currency::{strtolower($code)}()->toNative());
    }

    public function testMagicFactoryIsCaseInsensitive(): void
    {
        self::assertSame('USD', self::make(Currency::class, 'usd')->toNative());
        self::assertSame('USD', self::make(Currency::class, 'Usd')->toNative());
    }

    public function testMagicFactoryRejectsUnknownCode(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Unknown currency code "XYZ"');

        self::make(Currency::class, 'XYZ');
    }

    public function testEqualsComparesCodeOnly(): void
    {
        self::assertTrue(Currency::USD()->equals(Currency::fromString('USD')));
        self::assertFalse(Currency::USD()->equals(Currency::EUR()));
        self::assertFalse(Currency::USD()->equals(Currency::fromString('usd')));
        self::assertFalse(Currency::fromString('1')->equals(Currency::fromString('1.0')));
    }

    public function testEqualsIgnoresClassWhileSameValueAsDoesNot(): void
    {
        $base = Currency::USD();
        $sub = CompositeCurrency::USD();

        self::assertTrue($base->equals($sub));
        self::assertTrue($sub->equals($base));
        self::assertTrue($base->sameValueAs(Currency::USD()));
        self::assertFalse($base->sameValueAs($sub));
    }

    public function testFactoriesReturnTheSubclass(): void
    {
        $class = CompositeCurrency::class;

        self::assertInstanceOf($class, self::make($class, 'USD'));
        self::assertInstanceOf($class, self::make($class, 'fromString', 'x'));
        self::assertInstanceOf($class, self::make($class, 'fromCode', CurrencyCode::EUR));
        self::assertInstanceOf($class, self::make($class, 'fromNative', 'x'));
    }
}
