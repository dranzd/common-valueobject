<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Core;

use Dranzd\Common\Domain\ValueObject\Collection\Collection;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Exception\ToNativeValueIncapable;
use Dranzd\Common\Domain\ValueObject\Identity\Uuid;
use Dranzd\Common\Domain\ValueObject\Native\Logic\Boolean;
use Dranzd\Common\Domain\ValueObject\Native\Number\Integer;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreArrayValue;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreBare;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreDuckTyped;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreIncapable;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreMixedNative;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreOrderId;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CorePlainObject;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreProbe;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreSku;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversTrait(CompareSameValueTrait::class)]
#[UsesClass(Literal::class)]
#[UsesClass(Uuid::class)]
#[UsesClass(Integer::class)]
#[UsesClass(Boolean::class)]
#[UsesClass(Collection::class)]
#[UsesClass(ToNativeValueIncapable::class)]
#[UsesClass(CoreArrayValue::class)]
#[UsesClass(CoreBare::class)]
#[UsesClass(CoreDuckTyped::class)]
#[UsesClass(CoreIncapable::class)]
#[UsesClass(CoreMixedNative::class)]
#[UsesClass(CoreProbe::class)]
#[UsesClass(CorePlainObject::class)]
#[UsesClass(CoreOrderId::class)]
#[UsesClass(CoreSku::class)]
final class CompareSameValueTraitTest extends TestCase
{
    public function testSameClassAndSameNativeValueAreEqual(): void
    {
        self::assertTrue((new Literal('a'))->sameValueAs(new Literal('a')));
    }

    public function testSameClassAndDifferentNativeValueAreNotEqual(): void
    {
        self::assertFalse((new Literal('a'))->sameValueAs(new Literal('b')));
    }

    public function testNativeComparisonOfPrimitives(): void
    {
        self::assertFalse((new Integer(1))->sameValueAs(new Integer(2)));
        self::assertTrue((new Boolean(true))->sameValueAs(new Boolean(true)));
    }

    #[DataProvider('provideLooselyEqualNatives')]
    public function testNativeComparisonIsStrict(mixed $first, mixed $second): void
    {
        self::assertFalse((new CoreMixedNative($first))->sameValueAs(new CoreMixedNative($second)));
        self::assertTrue((new CoreMixedNative($first))->sameValueAs(new CoreMixedNative($first)));
    }

    /**
     * Pairs that are equal under `==`, so only a strict `===` tells them apart.
     *
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function provideLooselyEqualNatives(): iterable
    {
        yield 'int and numeric string' => [1, '1'];
        yield 'int and float' => [1, 1.0];
        yield 'zero and false' => [0, false];
        yield 'zero and empty string' => [0, ''];
        yield 'null and false' => [null, false];
        yield 'padded numeric strings' => ['1', '01'];
    }

    public function testDuckTypedNumericStringsAreNotEqual(): void
    {
        self::assertFalse((new CoreDuckTyped('1'))->sameValueAs(new CoreDuckTyped('01')));
    }

    #[DataProvider('provideNonObjects')]
    public function testNonObjectArgumentIsNotEqualInsteadOfFailing(mixed $other): void
    {
        // The previous release raised a TypeError here; false is the answer now.
        self::assertFalse((new CoreProbe())->probe($other));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function provideNonObjects(): iterable
    {
        yield 'null' => [null];
        yield 'string' => ['probe'];
        yield 'int' => [1];
        yield 'bool' => [true];
        yield 'array' => [[]];
    }

    public function testObjectOfAnotherClassIsNotEqualAndNotAskedForItsNativeValue(): void
    {
        self::assertFalse((new CoreProbe())->probe(new CorePlainObject()));
    }

    public function testDifferentClassWithSameNativeValueIsNotEqual(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        self::assertFalse((new Literal($uuid))->sameValueAs(new Uuid($uuid)));
        self::assertFalse((new Uuid($uuid))->sameValueAs(new Literal($uuid)));
    }

    public function testSubclassAndParentAreNotEqualInEitherDirection(): void
    {
        self::assertFalse((new Literal('a'))->sameValueAs(new CoreSku('a')));
        self::assertFalse((new CoreSku('a'))->sameValueAs(new Literal('a')));
        self::assertTrue((new CoreSku('a'))->sameValueAs(new CoreSku('a')));
    }

    public function testSubclassOfUuidIsNotEqualToUuidWithSameValue(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        self::assertFalse((new Uuid($uuid))->sameValueAs(new CoreOrderId($uuid)));
        self::assertFalse((new CoreOrderId($uuid))->sameValueAs(new Uuid($uuid)));
    }

    public function testObjectIncapableOfNativeValueIsNeverEqual(): void
    {
        $incapable = new CoreIncapable();

        self::assertFalse($incapable->sameValueAs($incapable));
        self::assertFalse($incapable->sameValueAs(new CoreIncapable()));
    }

    public function testObjectWithoutAnyTranslationIsNeverEqual(): void
    {
        // The previous release raised an Error because the object has no toNative(); false is the answer now.
        $bare = new CoreBare();

        self::assertFalse($bare->sameValueAs($bare));
    }

    public function testDuckTypedObjectIsComparedByItsNativeValue(): void
    {
        self::assertTrue((new CoreDuckTyped('x'))->sameValueAs(new CoreDuckTyped('x')));
        self::assertFalse((new CoreDuckTyped('x'))->sameValueAs(new CoreDuckTyped('y')));
    }

    public function testArrayValueObjectsAreComparedByTheirArray(): void
    {
        self::assertTrue((new CoreArrayValue(['a' => 1]))->sameValueAs(new CoreArrayValue(['a' => 1])));
        self::assertFalse((new CoreArrayValue(['a' => 1]))->sameValueAs(new CoreArrayValue(['a' => 2])));
    }

    public function testArrayComparisonIsStrictAboutOrderAndTypes(): void
    {
        $ordered = new CoreArrayValue(['a' => 1, 'b' => 2]);

        self::assertFalse($ordered->sameValueAs(new CoreArrayValue(['b' => 2, 'a' => 1])));
        self::assertFalse((new CoreArrayValue([1]))->sameValueAs(new CoreArrayValue(['1'])));
    }

    public function testArrayValueObjectIsNotEqualToAnotherClass(): void
    {
        self::assertFalse((new CoreArrayValue([]))->sameValueAs(new Collection([])));
        self::assertFalse((new Collection([]))->sameValueAs(new CoreArrayValue([])));
    }
}
