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

    public function testAdd(): void
    {
        $a = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);
        $b = Money::fromArray(['amount' => 3000, 'currency' => 'USD']);

        $result = $a->add($b);

        $this->assertSame(8000, $result->getAmount()->toNative());
        $this->assertSame('USD', $result->getCurrency()->toNative());
    }

    public function testSubtract(): void
    {
        $a = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);
        $b = Money::fromArray(['amount' => 3000, 'currency' => 'USD']);

        $result = $a->subtract($b);

        $this->assertSame(2000, $result->getAmount()->toNative());
    }

    public function testSubtractCanGoNegative(): void
    {
        $a = Money::fromArray(['amount' => 3000, 'currency' => 'USD']);
        $b = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);

        $result = $a->subtract($b);

        $this->assertSame(-2000, $result->getAmount()->toNative());
        $this->assertTrue($result->isNegative());
    }

    public function testMultiply(): void
    {
        $money = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);

        $result = $money->multiply(3);

        $this->assertSame(15000, $result->getAmount()->toNative());
    }

    public function testIsZero(): void
    {
        $this->assertTrue(Money::fromArray(['amount' => 0, 'currency' => 'USD'])->isZero());
        $this->assertFalse(Money::fromArray(['amount' => 1, 'currency' => 'USD'])->isZero());
    }

    public function testIsPositive(): void
    {
        $this->assertTrue(Money::fromArray(['amount' => 1, 'currency' => 'USD'])->isPositive());
        $this->assertFalse(Money::fromArray(['amount' => 0, 'currency' => 'USD'])->isPositive());
        $this->assertFalse(Money::fromArray(['amount' => -1, 'currency' => 'USD'])->isPositive());
    }

    public function testIsNegative(): void
    {
        $this->assertTrue(Money::fromArray(['amount' => -1, 'currency' => 'USD'])->isNegative());
        $this->assertFalse(Money::fromArray(['amount' => 0, 'currency' => 'USD'])->isNegative());
        $this->assertFalse(Money::fromArray(['amount' => 1, 'currency' => 'USD'])->isNegative());
    }

    public function testIsGreaterThan(): void
    {
        $a = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);
        $b = Money::fromArray(['amount' => 3000, 'currency' => 'USD']);

        $this->assertTrue($a->isGreaterThan($b));
        $this->assertFalse($b->isGreaterThan($a));
    }

    public function testIsGreaterThanOrEqual(): void
    {
        $a = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);
        $b = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);
        $c = Money::fromArray(['amount' => 3000, 'currency' => 'USD']);

        $this->assertTrue($a->isGreaterThanOrEqual($b));
        $this->assertTrue($a->isGreaterThanOrEqual($c));
        $this->assertFalse($c->isGreaterThanOrEqual($a));
    }

    public function testIsLessThan(): void
    {
        $a = Money::fromArray(['amount' => 3000, 'currency' => 'USD']);
        $b = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);

        $this->assertTrue($a->isLessThan($b));
        $this->assertFalse($b->isLessThan($a));
    }

    public function testEquals(): void
    {
        $a = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);
        $b = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);

        $this->assertTrue($a->equals($b));
    }

    public function testNotEqualsDifferentAmount(): void
    {
        $a = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);
        $b = Money::fromArray(['amount' => 3000, 'currency' => 'USD']);

        $this->assertFalse($a->equals($b));
    }

    public function testNotEqualsDifferentCurrency(): void
    {
        $a = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);
        $b = Money::fromArray(['amount' => 5000, 'currency' => 'EUR']);

        $this->assertFalse($a->equals($b));
    }

    public function testAddRejectsCurrencyMismatch(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Money::fromArray(['amount' => 5000, 'currency' => 'USD'])
            ->add(Money::fromArray(['amount' => 3000, 'currency' => 'EUR']));
    }

    public function testSubtractRejectsCurrencyMismatch(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Money::fromArray(['amount' => 5000, 'currency' => 'USD'])
            ->subtract(Money::fromArray(['amount' => 3000, 'currency' => 'EUR']));
    }

    public function testComparisonRejectsCurrencyMismatch(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Money::fromArray(['amount' => 5000, 'currency' => 'USD'])
            ->isGreaterThan(Money::fromArray(['amount' => 3000, 'currency' => 'EUR']));
    }

    public function testImmutability(): void
    {
        $original = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);
        $result = $original->add(Money::fromArray(['amount' => 3000, 'currency' => 'USD']));

        $this->assertSame(5000, $original->getAmount()->toNative());
        $this->assertSame(8000, $result->getAmount()->toNative());
    }
}
