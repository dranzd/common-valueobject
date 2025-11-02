<?php

namespace Dranzd\Common\Domain\ValueObject\Tests\Domain\ValueObject\Money;

use Dranzd\Common\Domain\ValueObject\Money\Basic as Money;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testCanCreateFromArray(): void
    {
        $money = Money::fromArray([
            'amount' => 1000,
            'currency' => 'USD'
        ]);

        $this->assertInstanceOf(Money::class, $money);
        $array = $money->toArray();
        $this->assertSame(1000, $array['amount']);
        $this->assertSame('USD', $array['currency']);
    }

    public function testCanConvertToArray(): void
    {
        $money = Money::fromArray([
            'amount' => 1000,
            'currency' => 'USD'
        ]);

        $array = $money->toArray();

        $this->assertSame(['amount' => 1000, 'currency' => 'USD'], $array);
    }

    public function testTwoMoneyObjectsWithSameValueAreEqual(): void
    {
        $money1 = Money::fromArray(['amount' => 1000, 'currency' => 'USD']);
        $money2 = Money::fromArray(['amount' => 1000, 'currency' => 'USD']);

        $this->assertTrue($money1->sameValueAs($money2));
    }

    public function testTwoMoneyObjectsWithDifferentAmountAreNotEqual(): void
    {
        $money1 = Money::fromArray(['amount' => 1000, 'currency' => 'USD']);
        $money2 = Money::fromArray(['amount' => 2000, 'currency' => 'USD']);

        $this->assertFalse($money1->sameValueAs($money2));
    }

    public function testTwoMoneyObjectsWithDifferentCurrencyAreNotEqual(): void
    {
        $money1 = Money::fromArray(['amount' => 1000, 'currency' => 'USD']);
        $money2 = Money::fromArray(['amount' => 1000, 'currency' => 'EUR']);

        $this->assertFalse($money1->sameValueAs($money2));
    }
}
