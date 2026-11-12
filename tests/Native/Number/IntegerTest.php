<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Native\Number;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Native\Number\Integer;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreCount;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Integer::class)]
#[UsesTrait(CompareSameValueTrait::class)]
#[UsesClass(CoreCount::class)]
final class IntegerTest extends TestCase
{
    public function testFromNativeAndToNative(): void
    {
        self::assertSame(42, Integer::fromNative(42)->toNative());
        self::assertSame(-42, Integer::fromNative(-42)->toNative());
        self::assertSame(0, Integer::fromNative(0)->toNative());
    }

    public function testFromNativeCoercesNumericStringsBecauseStrictTypesAreOffOnPurpose(): void
    {
        self::assertSame(5, Integer::fromNative('5')->toNative());
        self::assertSame(1, Integer::fromNative(true)->toNative());
    }

    public function testFromNativeRejectsNonNumericValues(): void
    {
        $this->expectException(\TypeError::class);

        Integer::fromNative('five');
    }

    public function testArithmeticOverflowRaisesATypeError(): void
    {
        $this->expectException(\TypeError::class);

        (new Integer(\PHP_INT_MAX))->plus(new Integer(1));
    }

    public function testToString(): void
    {
        self::assertSame('Integer value object of 7', (string) new Integer(7));
    }

    public function testSameValueAs(): void
    {
        self::assertTrue((new Integer(1))->sameValueAs(new Integer(1)));
        self::assertFalse((new Integer(1))->sameValueAs(new Integer(2)));
    }

    public function testIsEmptyAndIsNegative(): void
    {
        self::assertTrue((new Integer(0))->isEmpty());
        self::assertFalse((new Integer(1))->isEmpty());
        self::assertTrue((new Integer(-1))->isNegative());
        self::assertFalse((new Integer(0))->isNegative());
    }

    public function testArithmetic(): void
    {
        self::assertSame(7, (new Integer(3))->plus(new Integer(4))->toNative());
        self::assertSame(-1, (new Integer(3))->minus(new Integer(4))->toNative());
        self::assertSame(12, (new Integer(3))->multiply(new Integer(4))->toNative());
    }

    public function testComparisons(): void
    {
        $three = new Integer(3);
        $four = new Integer(4);

        self::assertTrue($four->isGreaterThan($three));
        self::assertFalse($three->isGreaterThan($three));
        self::assertTrue($three->isLessThan($four));
        self::assertFalse($three->isLessThan($three));
        self::assertTrue($three->isGreaterThanOrEqualTo($three));
        self::assertFalse($three->isGreaterThanOrEqualTo($four));
        self::assertTrue($three->isLessThanOrEqualTo($three));
        self::assertFalse($four->isLessThanOrEqualTo($three));
        self::assertTrue($three->isEqualTo(new Integer(3)));
        self::assertFalse($three->isEqualTo($four));
        self::assertTrue($three->isNotEqualTo($four));
        self::assertFalse($three->isNotEqualTo(new Integer(3)));
    }

    public function testFactoryAndArithmeticReturnTheCalledClass(): void
    {
        $count = CoreCount::fromNative(5);

        self::assertSame(CoreCount::class, ($count)::class);
        self::assertSame(CoreCount::class, ($count->plus(new Integer(1)))::class);
        self::assertSame(CoreCount::class, ($count->minus(new Integer(1)))::class);
        self::assertSame(CoreCount::class, ($count->multiply(new Integer(2)))::class);
        self::assertSame(6, $count->plus(new Integer(1))->raw());
    }
}
