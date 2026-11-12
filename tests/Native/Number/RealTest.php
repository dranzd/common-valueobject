<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Native\Number;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Native\Number\Real;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreRatio;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Real::class)]
#[UsesTrait(CompareSameValueTrait::class)]
#[UsesClass(CoreRatio::class)]
final class RealTest extends TestCase
{
    public function testDefaultsToZero(): void
    {
        self::assertSame(0.0, (new Real())->toNative());
        self::assertSame(0.0, Real::withNoValue()->toNative());
    }

    public function testFromNativeAndToNative(): void
    {
        self::assertSame(1.5, Real::fromNative(1.5)->toNative());
    }

    public function testFromNativeCoercesNumericStringsBecauseStrictTypesAreOffOnPurpose(): void
    {
        self::assertSame(1.5, Real::fromNative('1.5')->toNative());
        self::assertSame(2.0, Real::fromNative(2)->toNative());
    }

    public function testNotANumberIsNotTheSameValueAsItself(): void
    {
        $nan = new Real(\NAN);

        self::assertFalse($nan->sameValueAs($nan));
        self::assertFalse($nan->sameValueAs(new Real(\NAN)));
        self::assertFalse($nan->isEqualTo($nan));
    }

    public function testToString(): void
    {
        self::assertSame('Real value object of 1.5', (string) new Real(1.5));
    }

    public function testSameValueAs(): void
    {
        self::assertTrue((new Real(1.5))->sameValueAs(new Real(1.5)));
        self::assertFalse((new Real(1.5))->sameValueAs(new Real(2.5)));
    }

    public function testIsEmptyIsTrueOnlyForZero(): void
    {
        self::assertTrue((new Real(0.0))->isEmpty());
        self::assertTrue(Real::withNoValue()->isEmpty());
        self::assertTrue((new Real())->isEmpty());
        self::assertFalse((new Real(0.1))->isEmpty());
        self::assertFalse((new Real(-0.1))->isEmpty());
    }

    public function testIsNegative(): void
    {
        self::assertTrue((new Real(-0.5))->isNegative());
        self::assertFalse((new Real(0.0))->isNegative());
    }

    public function testArithmetic(): void
    {
        self::assertSame(4.0, (new Real(1.5))->plus(new Real(2.5))->toNative());
        self::assertSame(-1.0, (new Real(1.5))->minus(new Real(2.5))->toNative());
        self::assertSame(3.75, (new Real(1.5))->multiply(new Real(2.5))->toNative());
    }

    public function testComparisons(): void
    {
        $small = new Real(1.5);
        $big = new Real(2.5);

        self::assertTrue($big->isGreaterThan($small));
        self::assertFalse($small->isGreaterThan($small));
        self::assertTrue($small->isLessThan($big));
        self::assertFalse($small->isLessThan($small));
        self::assertTrue($small->isGreaterThanOrEqualTo($small));
        self::assertFalse($small->isGreaterThanOrEqualTo($big));
        self::assertTrue($small->isLessThanOrEqualTo($small));
        self::assertFalse($big->isLessThanOrEqualTo($small));
        self::assertTrue($small->isEqualTo(new Real(1.5)));
        self::assertFalse($small->isEqualTo($big));
        self::assertTrue($small->isNotEqualTo($big));
        self::assertFalse($small->isNotEqualTo(new Real(1.5)));
    }

    public function testFactoriesAndArithmeticReturnTheCalledClass(): void
    {
        $ratio = CoreRatio::fromNative(0.5);

        self::assertSame(CoreRatio::class, ($ratio)::class);
        self::assertSame(CoreRatio::class, (CoreRatio::withNoValue())::class);
        self::assertSame(CoreRatio::class, ($ratio->plus(new Real(1.0)))::class);
        self::assertSame(CoreRatio::class, ($ratio->minus(new Real(1.0)))::class);
        self::assertSame(CoreRatio::class, ($ratio->multiply(new Real(2.0)))::class);
        self::assertSame(1.5, $ratio->plus(new Real(1.0))->raw());
    }
}
