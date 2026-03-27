<?php

namespace Dranzd\Common\Domain\ValueObject\Tests\Domain\ValueObject\Money;

use Dranzd\Common\Domain\ValueObject\Money\Currency;
use Dranzd\Common\Domain\ValueObject\Money\CurrencyCode;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function testCanCreateFromString(): void
    {
        $currency = Currency::fromString('USD');

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertSame('USD', $currency->toString());
    }

    public function testToStringReturnsSameAsToNative(): void
    {
        $currency = Currency::fromString('EUR');

        $this->assertSame($currency->toNative(), $currency->toString());
    }

    public function testEqualsSameCurrency(): void
    {
        $a = Currency::fromString('USD');
        $b = Currency::fromString('USD');

        $this->assertTrue($a->equals($b));
    }

    public function testNotEqualsDifferentCurrency(): void
    {
        $a = Currency::fromString('USD');
        $b = Currency::fromString('EUR');

        $this->assertFalse($a->equals($b));
    }

    public function testSameValueAs(): void
    {
        $a = Currency::fromString('USD');
        $b = Currency::fromString('USD');

        $this->assertTrue($a->sameValueAs($b));
    }

    public function testIsEmptyForBlankCurrency(): void
    {
        $currency = Currency::fromString('');

        $this->assertTrue($currency->isEmpty());
    }

    public function testFromNativeBackwardCompatibility(): void
    {
        $currency = Currency::fromNative('USD');

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertSame('USD', $currency->toNative());
    }

    public function testCallStaticCreatesInstance(): void
    {
        $usd = Currency::USD();
        $eur = Currency::EUR();
        $php = Currency::PHP();

        $this->assertInstanceOf(Currency::class, $usd);
        $this->assertSame('USD', $usd->toNative());
        $this->assertSame('EUR', $eur->toNative());
        $this->assertSame('PHP', $php->toNative());
    }

    public function testCallStaticIsCaseInsensitive(): void
    {
        $currency = Currency::usd();

        $this->assertSame('USD', $currency->toNative());
    }

    public function testCallStaticRejectsUnknownCode(): void
    {
        $this->expectException(\BadMethodCallException::class);

        Currency::XYZ();
    }

    public function testFromCodeCreatesCurrency(): void
    {
        $currency = Currency::fromCode(CurrencyCode::USD);

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertSame('USD', $currency->toNative());
    }
}
