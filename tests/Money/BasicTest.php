<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Money;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Internal\ArrayKeys;
use Dranzd\Common\Domain\ValueObject\Money\Basic;
use Dranzd\Common\Domain\ValueObject\Money\Currency;
use Dranzd\Common\Domain\ValueObject\Native\Number\Integer;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CompositeCurrency;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CompositeCurrencyPrice;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CompositePrice;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CompositeTrackedPrice;
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Basic::class)]
#[UsesClass(ArrayKeys::class)]
#[UsesClass(Currency::class)]
#[UsesClass(Integer::class)]
#[UsesClass(Literal::class)]
#[UsesClass(CompositeCurrency::class)]
#[UsesClass(CompositeCurrencyPrice::class)]
#[UsesClass(CompositePrice::class)]
#[UsesClass(CompositeTrackedPrice::class)]
#[UsesTrait(CompareSameValueTrait::class)]
#[UsesTrait(ToStringUsingObjectHashTrait::class)]
final class BasicTest extends TestCase
{
    /**
     * @return array<string, array{array<mixed>, array{amount: int, currency: string}}>
     */
    public static function validInputProvider(): array
    {
        return [
            // Pinned for compatibility: element 0 being null still makes it a list, and null casts to 0.
            'positional with null amount' => [[null, 'USD'], ['amount' => 0, 'currency' => 'USD']],
            'positional with all values taken in order' => [
                [100, 'currency' => 'USD'],
                ['amount' => 100, 'currency' => 'USD'],
            ],
            'positional with surplus elements' => [[100, 'USD', 'ignored'], ['amount' => 100, 'currency' => 'USD']],
            'positional zero amount' => [[0, 'EUR'], ['amount' => 0, 'currency' => 'EUR']],
            'positional non-sequential keys' => [[0 => 100, 5 => 'USD'], ['amount' => 100, 'currency' => 'USD']],
            'positional string amount' => [['100', 'USD'], ['amount' => 100, 'currency' => 'USD']],
            'positional fractional amount is truncated' => [[100.9, 'USD'], ['amount' => 100, 'currency' => 'USD']],
            'named' => [['amount' => 100, 'currency' => 'USD'], ['amount' => 100, 'currency' => 'USD']],
            'named string amount' => [['amount' => '100', 'currency' => 'USD'], ['amount' => 100, 'currency' => 'USD']],
            'named PascalCase keys' => [['Amount' => 7, 'Currency' => 'EUR'], ['amount' => 7, 'currency' => 'EUR']],
            'named key that only resembles currency is surplus' => [
                ['amount' => 7, 'Cur_rency' => 'EUR', 'currency' => 'GBP'],
                ['amount' => 7, 'currency' => 'GBP'],
            ],
            // Pinned for compatibility: when two keys convert to the same name the later one wins.
            'named colliding keys, later wins' => [
                ['amount' => 100, 'currency' => 'USD', 'Currency' => 'EUR'],
                ['amount' => 100, 'currency' => 'EUR'],
            ],
            'named colliding keys, later wins (reversed)' => [
                ['Currency' => 'EUR', 'amount' => 100, 'currency' => 'USD'],
                ['amount' => 100, 'currency' => 'USD'],
            ],
            'named surplus keys' => [
                ['amount' => 1, 'currency' => 'USD', 'note' => 'x'],
                ['amount' => 1, 'currency' => 'USD'],
            ],
            'named null currency becomes empty' => [
                ['amount' => 1, 'currency' => null],
                ['amount' => 1, 'currency' => ''],
            ],
            'negative' => [[-250, 'USD'], ['amount' => -250, 'currency' => 'USD']],
            'unknown currency code is accepted' => [[1, 'xyz'], ['amount' => 1, 'currency' => 'xyz']],
        ];
    }

    /**
     * @param array<mixed> $input
     * @param array{amount: int, currency: string} $expected
     */
    #[DataProvider('validInputProvider')]
    public function testFromArrayBuildsMoney(array $input, array $expected): void
    {
        $money = Basic::fromArray($input);

        self::assertSame($expected, $money->toArray());
        self::assertSame($expected['amount'], $money->getAmount()->toNative());
        self::assertSame($expected['currency'], $money->getCurrency()->toNative());
    }

    public function testFromArrayPositionalInputRaisesNoDeprecation(): void
    {
        // The suite fails on any deprecation, so this passes only if building from a list raises none.
        self::assertSame(['amount' => 100, 'currency' => 'USD'], Basic::fromArray([100, 'USD'])->toArray());
    }

    public function testOfNoValueIsZeroWithEmptyCurrency(): void
    {
        $money = Basic::ofNoValue();

        self::assertSame(['amount' => 0, 'currency' => ''], $money->toArray());
        self::assertTrue($money->isZero());
    }

    public function testFromArrayWithTooFewPositionalElementsNamesTheArity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Money needs two arguments: amount and currency.');

        Basic::fromArray([100]);
    }

    public function testFromArrayWithAllCapsKeyDoesNotMatch(): void
    {
        // Pinned for compatibility: 'CURRENCY' converts to 'cURRENCY', which is not 'currency'.
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "currency"');

        Basic::fromArray(['amount' => 1, 'CURRENCY' => 'USD']);
    }

    public function testFromArrayWithEmptyArrayNamesFirstMissingKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "amount"');

        Basic::fromArray([]);
    }

    public function testFromArrayWithMissingCurrencyKeyNamesKeyAndAcceptedKeys(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "currency"; accepted keys: amount, currency');

        Basic::fromArray(['amount' => 100]);
    }

    public function testFromArrayWithMissingAmountKeyThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "amount"');

        Basic::fromArray(['currency' => 'USD']);
    }

    public function testFromArrayReturnsSubclassInstances(): void
    {
        $positional = self::build(CompositePrice::class, [100, 'USD']);
        $named = self::build(CompositePrice::class, ['amount' => 100, 'currency' => 'USD']);

        self::assertInstanceOf(CompositePrice::class, $positional);
        self::assertInstanceOf(CompositePrice::class, $named);
        self::assertInstanceOf(CompositePrice::class, CompositePrice::ofNoValue());
        self::assertSame('100 USD', CompositePrice::fromArray([100, 'USD'])->describe());
    }

    /**
     * @param class-string<Basic> $class
     * @param array<array-key, mixed> $input
     */
    private static function build(string $class, array $input): Basic
    {
        return $class::fromArray($input);
    }

    public function testArithmeticOnSubclassReturnsSubclass(): void
    {
        $price = CompositePrice::fromArray([100, 'USD']);

        self::assertInstanceOf(CompositePrice::class, $price->add($price));
        self::assertInstanceOf(CompositePrice::class, $price->subtract($price));
        self::assertInstanceOf(CompositePrice::class, $price->multiply(2));
    }

    public function testFromArrayUsesLateStaticBoundFromNative(): void
    {
        CompositeTrackedPrice::$calls = [];

        CompositeTrackedPrice::fromArray([100, 'USD', 'surplus']);
        CompositeTrackedPrice::fromArray([5 => 'x', 0 => 7, 'k' => 'USD']);
        CompositeTrackedPrice::fromArray(['amount' => 5, 'currency' => 'EUR']);

        self::assertSame([[100, 'USD', 'surplus'], ['x', 7, 'USD'], [5, 'EUR']], CompositeTrackedPrice::$calls);
    }

    public function testSubclassMayOverrideIsLessThanOrEqual(): void
    {
        $a = CompositeTrackedPrice::fromArray([1, 'USD']);
        $b = CompositeTrackedPrice::fromArray([2, 'USD']);

        self::assertTrue($a->isLessThanOrEqual($b));
    }

    public function testSameValueAsIsClassSensitiveButEqualsIsNot(): void
    {
        $base = Basic::fromArray([100, 'USD']);
        $sub = CompositePrice::fromArray([100, 'USD']);

        self::assertTrue($base->sameValueAs(Basic::fromArray([100, 'USD'])));
        self::assertFalse($base->sameValueAs($sub));
        self::assertFalse($sub->sameValueAs($base));
        self::assertTrue($base->equals($sub));
        self::assertTrue($sub->equals($base));
    }

    public function testSameValueAsAndEqualsDistinguishAmountAndCurrency(): void
    {
        $base = Basic::fromArray([100, 'USD']);

        self::assertFalse($base->sameValueAs(Basic::fromArray([101, 'USD'])));
        self::assertFalse($base->sameValueAs(Basic::fromArray([100, 'EUR'])));
        self::assertFalse($base->equals(Basic::fromArray([101, 'USD'])));
        self::assertFalse($base->equals(Basic::fromArray([100, 'EUR'])));
    }

    public function testEqualsNeverThrowsOnCurrencyMismatch(): void
    {
        self::assertFalse(Basic::fromArray([1, 'USD'])->equals(Basic::fromArray([1, 'EUR'])));
    }

    public function testCurrencyIsComparedByCodeNotByClass(): void
    {
        $base = Basic::fromArray([100, 'PHP']);
        $sub = CompositeCurrencyPrice::of(100, 'PHP');

        self::assertTrue($base->equals($sub));
        self::assertTrue($sub->equals($base));
        self::assertFalse($base->sameValueAs($sub));
        self::assertSame(200, $base->add($sub)->getAmount()->toNative());
        self::assertSame(0, $sub->subtract($base)->getAmount()->toNative());
        self::assertSame(200, $sub->add($base)->getAmount()->toNative());
        self::assertFalse($base->isGreaterThan($sub));
        self::assertTrue($base->isGreaterThanOrEqual($sub));
        self::assertFalse($sub->isLessThan($base));
        self::assertTrue($sub->isLessThanOrEqual($base));
    }

    public function testDifferentCodesStillMismatchAcrossCurrencyClasses(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency mismatch: cannot operate on PHP and USD');

        CompositeCurrencyPrice::of(1, 'PHP')->add(Basic::fromArray([1, 'USD']));
    }

    public function testArithmetic(): void
    {
        $a = Basic::fromArray([5000, 'USD']);
        $b = Basic::fromArray([3000, 'USD']);

        self::assertSame(8000, $a->add($b)->getAmount()->toNative());
        self::assertSame(2000, $a->subtract($b)->getAmount()->toNative());
        self::assertSame(-2000, $b->subtract($a)->getAmount()->toNative());
        self::assertSame(15000, $a->multiply(3)->getAmount()->toNative());
        self::assertSame(-5000, $a->multiply(-1)->getAmount()->toNative());
        self::assertSame('USD', $a->add($b)->getCurrency()->toNative());
    }

    public function testArithmeticDoesNotMutateOperands(): void
    {
        $a = Basic::fromArray([5000, 'USD']);
        $a->add(Basic::fromArray([1, 'USD']));
        $a->multiply(2);

        self::assertSame(5000, $a->getAmount()->toNative());
    }

    public function testSignPredicates(): void
    {
        $zero = Basic::fromArray([0, 'USD']);
        $positive = Basic::fromArray([1, 'USD']);
        $negative = Basic::fromArray([-1, 'USD']);

        self::assertTrue($zero->isZero());
        self::assertFalse($positive->isZero());
        self::assertFalse($negative->isZero());
        self::assertTrue($positive->isPositive());
        self::assertFalse($zero->isPositive());
        self::assertFalse($negative->isPositive());
        self::assertTrue($negative->isNegative());
        self::assertFalse($zero->isNegative());
        self::assertFalse($positive->isNegative());
    }

    public function testComparisons(): void
    {
        $small = Basic::fromArray([3000, 'USD']);
        $large = Basic::fromArray([5000, 'USD']);
        $largeToo = Basic::fromArray([5000, 'USD']);

        self::assertTrue($large->isGreaterThan($small));
        self::assertFalse($small->isGreaterThan($large));
        self::assertFalse($large->isGreaterThan($largeToo));

        self::assertTrue($large->isGreaterThanOrEqual($small));
        self::assertTrue($large->isGreaterThanOrEqual($largeToo));
        self::assertFalse($small->isGreaterThanOrEqual($large));

        self::assertTrue($small->isLessThan($large));
        self::assertFalse($large->isLessThan($small));
        self::assertFalse($large->isLessThan($largeToo));

        self::assertTrue($small->isLessThanOrEqual($large));
        self::assertTrue($large->isLessThanOrEqual($largeToo));
        self::assertFalse($large->isLessThanOrEqual($small));
    }

    /**
     * @return array<string, array{callable(Basic, Basic): mixed}>
     */
    public static function currencyMismatchProvider(): array
    {
        return [
            'add' => [static fn (Basic $a, Basic $b) => $a->add($b)],
            'subtract' => [static fn (Basic $a, Basic $b) => $a->subtract($b)],
            'isGreaterThan' => [static fn (Basic $a, Basic $b) => $a->isGreaterThan($b)],
            'isGreaterThanOrEqual' => [static fn (Basic $a, Basic $b) => $a->isGreaterThanOrEqual($b)],
            'isLessThan' => [static fn (Basic $a, Basic $b) => $a->isLessThan($b)],
            'isLessThanOrEqual' => [static fn (Basic $a, Basic $b) => $a->isLessThanOrEqual($b)],
        ];
    }

    /**
     * @param callable(Basic, Basic): mixed $operation
     */
    #[DataProvider('currencyMismatchProvider')]
    public function testCurrencyMismatchThrows(callable $operation): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency mismatch: cannot operate on USD and EUR');

        $operation(Basic::fromArray([1, 'USD']), Basic::fromArray([1, 'EUR']));
    }

    public function testAddOverflowThrowsTypeErrorInsteadOfCorrupting(): void
    {
        $this->expectException(\TypeError::class);

        Basic::fromArray([PHP_INT_MAX, 'USD'])->add(Basic::fromArray([1, 'USD']));
    }

    public function testSubtractOverflowThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        Basic::fromArray([PHP_INT_MIN, 'USD'])->subtract(Basic::fromArray([1025, 'USD']));
    }

    public function testAddingANegativeAmountBelowTheMinimumThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        Basic::fromArray([PHP_INT_MIN, 'USD'])->add(Basic::fromArray([-1025, 'USD']));
    }

    public function testAnOverflowBelowTheMinimumWithinAThousandUnitsSilentlyYieldsTheMinimum(): void
    {
        // Pinned known gap: the float result rounds back onto PHP_INT_MIN, so no TypeError is raised.
        $min = Basic::fromArray([PHP_INT_MIN, 'USD']);

        self::assertSame(PHP_INT_MIN, $min->subtract(Basic::fromArray([1, 'USD']))->getAmount()->toNative());
        self::assertSame(PHP_INT_MIN, $min->subtract(Basic::fromArray([1024, 'USD']))->getAmount()->toNative());
        self::assertSame(PHP_INT_MIN, $min->add(Basic::fromArray([-1024, 'USD']))->getAmount()->toNative());
    }

    public function testMultiplyOverflowThrowsTypeErrorInsteadOfCorrupting(): void
    {
        $this->expectException(\TypeError::class);

        Basic::fromArray([PHP_INT_MAX, 'USD'])->multiply(2);
    }

    public function testToStringUsesObjectHash(): void
    {
        $money = Basic::fromArray([1, 'USD']);

        self::assertStringContainsString(spl_object_hash($money), (string) $money);
    }

    public function testToArrayRoundTrips(): void
    {
        $money = Basic::fromArray([1234, 'PHP']);

        self::assertTrue($money->sameValueAs(Basic::fromArray($money->toArray())));
    }
}
