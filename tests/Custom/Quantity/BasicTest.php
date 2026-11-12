<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Custom\Quantity;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Custom\Quantity\Basic;
use Dranzd\Common\Domain\ValueObject\Internal\ArrayKeys;
use Dranzd\Common\Domain\ValueObject\Native\Number\Real;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CompositeQuantity;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CompositeTrackedQuantity;
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Basic::class)]
#[UsesClass(ArrayKeys::class)]
#[UsesClass(Real::class)]
#[UsesClass(Literal::class)]
#[UsesClass(CompositeQuantity::class)]
#[UsesClass(CompositeTrackedQuantity::class)]
#[UsesTrait(CompareSameValueTrait::class)]
#[UsesTrait(ToStringUsingObjectHashTrait::class)]
final class BasicTest extends TestCase
{
    /**
     * @return array<string, array{array<mixed>, float, string}>
     */
    public static function validInputProvider(): array
    {
        return [
            'positional integer' => [[2, 'pc'], 2.0, 'pc'],
            'positional zero' => [[0, ''], 0.0, ''],
            'positional fraction is preserved' => [[1.5, 'kg'], 1.5, 'kg'],
            'positional numeric string fraction' => [['1.5', 'kg'], 1.5, 'kg'],
            'positional surplus elements' => [[3, 'm', 'ignored'], 3.0, 'm'],
            // Pinned for compatibility: non-numeric strings cast to 0.0 rather than being rejected.
            'positional non-numeric string is zero' => [['abc', 'kg'], 0.0, 'kg'],
            'positional null value is zero' => [[null, 'kg'], 0.0, 'kg'],
            'positional non-sequential keys' => [[0 => 1.5, 5 => 'kg'], 1.5, 'kg'],
            'positional with all values taken in order' => [[1.5, 'unit' => 'kg'], 1.5, 'kg'],
            'positional null unit is empty' => [[1, null], 1.0, ''],
            'named' => [['value' => 2, 'unit' => 'pc'], 2.0, 'pc'],
            'named fraction' => [['value' => 0.25, 'unit' => 'l'], 0.25, 'l'],
            'named PascalCase keys' => [['Value' => 1, 'Unit' => 'kg'], 1.0, 'kg'],
            'named null unit is empty' => [['value' => 1, 'unit' => null], 1.0, ''],
            // Pinned for compatibility: when two keys convert to the same name the later one wins.
            'named colliding keys, later wins' => [['value' => 1, 'unit' => 'kg', 'Unit' => 'g'], 1.0, 'g'],
            'named surplus keys' => [['value' => 1, 'unit' => 'kg', 'note' => 'x'], 1.0, 'kg'],
            'negative fraction' => [[-2.75, 'kg'], -2.75, 'kg'],
        ];
    }

    /**
     * @param array<mixed> $input
     */
    #[DataProvider('validInputProvider')]
    public function testFromArray(array $input, float $value, string $unit): void
    {
        $quantity = Basic::fromArray($input);

        self::assertSame($value, $quantity->getValue()->toNative());
        self::assertSame($unit, $quantity->getUnit()->toNative());
        self::assertSame(['value' => $value, 'unit' => $unit], $quantity->toArray());
    }

    public function testOfNoValue(): void
    {
        self::assertSame(['value' => 0.0, 'unit' => ''], Basic::ofNoValue()->toArray());
    }

    public function testTooFewPositionalElementsNamesTheArity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity needs two arguments: value and unit.');

        Basic::fromArray([1.5]);
    }

    public function testMissingNamedKeyThrowsNamingTheKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "unit"; accepted keys: value, unit');

        Basic::fromArray(['value' => 1.5]);
    }

    public function testAllCapsKeyDoesNotMatch(): void
    {
        // Pinned for compatibility: 'UNIT' converts to 'uNIT', which is not 'unit'.
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "unit"');

        Basic::fromArray(['value' => 1, 'UNIT' => 'kg']);
    }

    /**
     * @return array<string, array{array<array-key, mixed>}>
     */
    public static function nonFiniteProvider(): array
    {
        return [
            'positional NAN' => [[NAN, 'kg']],
            'positional INF' => [[INF, 'kg']],
            'positional -INF' => [[-INF, 'kg']],
            'named NAN' => [['value' => NAN, 'unit' => 'kg']],
            'named INF' => [['value' => INF, 'unit' => 'kg']],
            'numeric string overflowing to INF' => [['1e999', 'kg']],
        ];
    }

    /**
     * @param array<array-key, mixed> $input
     */
    #[DataProvider('nonFiniteProvider')]
    public function testNonFiniteValueIsRejected(array $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity value must be a finite number.');

        Basic::fromArray($input);
    }

    public function testFromArrayUsesLateStaticBoundFromNative(): void
    {
        CompositeTrackedQuantity::$calls = [];

        CompositeTrackedQuantity::fromArray([1, 'kg', 'surplus']);
        CompositeTrackedQuantity::fromArray(['value' => 2, 'unit' => 'g']);

        self::assertSame([[1, 'kg', 'surplus'], [2, 'g']], CompositeTrackedQuantity::$calls);
    }

    public function testMissingValueKeyThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "value"');

        Basic::fromArray([]);
    }

    public function testSubclassGetsSubclassInstances(): void
    {
        $positional = self::build(CompositeQuantity::class, [1.5, 'kg']);
        $named = self::build(CompositeQuantity::class, ['value' => 1.5, 'unit' => 'kg']);

        self::assertInstanceOf(CompositeQuantity::class, $positional);
        self::assertInstanceOf(CompositeQuantity::class, $named);
        self::assertInstanceOf(CompositeQuantity::class, CompositeQuantity::ofNoValue());
        self::assertSame('1.5 kg', $positional->describe());
    }

    /**
     * @param class-string<Basic> $class
     * @param array<array-key, mixed> $input
     */
    private static function build(string $class, array $input): Basic
    {
        return $class::fromArray($input);
    }

    public function testSameValueAs(): void
    {
        $quantity = Basic::fromArray([1.5, 'kg']);

        self::assertTrue($quantity->sameValueAs(Basic::fromArray([1.5, 'kg'])));
        self::assertFalse($quantity->sameValueAs(Basic::fromArray([1.0, 'kg'])));
        self::assertFalse($quantity->sameValueAs(Basic::fromArray([1.5, 'g'])));
        self::assertFalse($quantity->sameValueAs(CompositeQuantity::fromArray([1.5, 'kg'])));
    }

    public function testToArrayRoundTrips(): void
    {
        $quantity = Basic::fromArray([12.125, 'kg']);

        self::assertTrue($quantity->sameValueAs(Basic::fromArray($quantity->toArray())));
    }

    public function testToStringUsesObjectHash(): void
    {
        $quantity = Basic::fromArray([1, 'kg']);

        self::assertStringContainsString(spl_object_hash($quantity), (string) $quantity);
    }
}
