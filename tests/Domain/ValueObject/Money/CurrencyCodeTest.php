<?php

namespace Dranzd\Common\Domain\ValueObject\Tests\Domain\ValueObject\Money;

use Dranzd\Common\Domain\ValueObject\Money\CurrencyCode;
use PHPUnit\Framework\TestCase;

class CurrencyCodeTest extends TestCase
{
    public function testEnumHasExpectedCases(): void
    {
        $this->assertSame('USD', CurrencyCode::USD->value);
        $this->assertSame('EUR', CurrencyCode::EUR->value);
        $this->assertSame('PHP', CurrencyCode::PHP->value);
        $this->assertSame('SGD', CurrencyCode::SGD->value);
    }

    public function testFromValidString(): void
    {
        $code = CurrencyCode::from('USD');

        $this->assertSame(CurrencyCode::USD, $code);
    }

    public function testFromInvalidStringThrows(): void
    {
        $this->expectException(\ValueError::class);

        CurrencyCode::from('XYZ');
    }

    public function testTryFromValidString(): void
    {
        $code = CurrencyCode::tryFrom('EUR');

        $this->assertSame(CurrencyCode::EUR, $code);
    }

    public function testTryFromInvalidStringReturnsNull(): void
    {
        $this->assertNull(CurrencyCode::tryFrom('XYZ'));
    }

    public function testCasesReturnsAllCodes(): void
    {
        $cases = CurrencyCode::cases();

        $this->assertNotEmpty($cases);
        $this->assertContains(CurrencyCode::USD, $cases);
        $this->assertContains(CurrencyCode::EUR, $cases);
    }
}
