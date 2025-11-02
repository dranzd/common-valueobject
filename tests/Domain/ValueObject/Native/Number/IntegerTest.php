<?php

namespace Dranzd\Common\Domain\ValueObject\Tests\Domain\ValueObject\Native\Number;

use Dranzd\Common\Domain\ValueObject\Native\Number\Integer;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    public function testCanCreateFromInteger(): void
    {
        $integer = Integer::fromNative(42);

        $this->assertInstanceOf(Integer::class, $integer);
        $this->assertSame(42, $integer->toNative());
    }

    public function testCanCreateFromZero(): void
    {
        $integer = Integer::fromNative(0);

        $this->assertSame(0, $integer->toNative());
    }

    public function testCanCreateFromNegativeNumber(): void
    {
        $integer = Integer::fromNative(-42);

        $this->assertSame(-42, $integer->toNative());
    }

    public function testTwoIntegersWithSameValueAreEqual(): void
    {
        $integer1 = Integer::fromNative(42);
        $integer2 = Integer::fromNative(42);

        $this->assertTrue($integer1->sameValueAs($integer2));
    }

    public function testTwoIntegersWithDifferentValueAreNotEqual(): void
    {
        $integer1 = Integer::fromNative(42);
        $integer2 = Integer::fromNative(43);

        $this->assertFalse($integer1->sameValueAs($integer2));
    }
}
