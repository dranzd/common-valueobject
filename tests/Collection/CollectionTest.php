<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Collection;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Collection\Collection;
use Dranzd\Common\Domain\ValueObject\Native\Number\Integer;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreCollection;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreIntegerCollection;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreSources;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Collection::class)]
#[UsesClass(Literal::class)]
#[UsesClass(Integer::class)]
#[UsesClass(CoreCollection::class)]
#[UsesClass(CoreIntegerCollection::class)]
#[UsesClass(CoreSources::class)]
#[UsesTrait(CompareSameValueTrait::class)]
final class CollectionTest extends TestCase
{
    public function testFromArrayWrapsTheArray(): void
    {
        $collection = Collection::fromArray(['a' => 1, 'b' => 2]);

        self::assertSame(Collection::class, $collection::class);
        self::assertSame(['a' => 1, 'b' => 2], $collection->toArray());
        self::assertCount(2, $collection);
    }

    public function testFromArrayAlwaysBuildsAPlainCollection(): void
    {
        $collection = CoreCollection::fromArray([1, 2, 3]);

        self::assertSame(Collection::class, $collection::class);
        self::assertSame([1, 2, 3], $collection->toArray());
    }

    public function testFromArrayDoesNotCallTheConstructorOfASubclass(): void
    {
        // The previous release built the base class, so a validating subclass constructor never ran.
        $collection = CoreIntegerCollection::fromArray(['not an integer']);

        self::assertSame(Collection::class, $collection::class);
        self::assertSame(['not an integer'], $collection->toArray());
    }

    public function testFromArrayResultIsTheSameValueAsAPlainCollection(): void
    {
        self::assertTrue(CoreCollection::fromArray([1])->sameValueAs(new Collection([1])));
        self::assertFalse(CoreCollection::fromArray([1])->sameValueAs(new CoreCollection([1])));
    }

    public function testSubclassesUseTheirOwnConstructor(): void
    {
        self::assertCount(2, new CoreIntegerCollection([1, 2]));

        $this->expectException(\InvalidArgumentException::class);

        new CoreIntegerCollection(['not an integer']);
    }

    public function testUntypedSubclassWorks(): void
    {
        $sources = new CoreSources(['a' => 1]);
        $sources->add(2);

        self::assertSame(['a' => 1, 2], $sources->toArray());
    }

    public function testToArrayKeepsScalarsAndObjectsWithoutTranslation(): void
    {
        $plain = new \stdClass();

        self::assertSame([1, 'two', null], (new Collection([1, 'two', null]))->toArray());
        self::assertSame([$plain], (new Collection([$plain]))->toArray());
    }

    public function testToArrayTranslatesObjectsWithToNative(): void
    {
        $collection = new Collection([new Literal('a'), new Integer(2)]);

        self::assertSame(['a', 2], $collection->toArray());
    }

    public function testToArrayTranslatesNestedCollectionsRecursively(): void
    {
        $collection = new Collection(['x' => new Collection([new Literal('a'), new Collection([1])])]);

        self::assertSame(['x' => ['a', [1]]], $collection->toArray());
    }

    public function testToArrayPrefersToArrayOverToNative(): void
    {
        $item = new class () {
            /**
             * @return array<string, string>
             */
            public function toArray(): array
            {
                return ['from' => 'toArray'];
            }

            public function toNative(): string
            {
                return 'from toNative';
            }
        };

        self::assertSame([['from' => 'toArray']], (new Collection([$item]))->toArray());
    }

    public function testSameValueAs(): void
    {
        $collection = new Collection([new Literal('a'), 1]);

        self::assertFalse($collection->sameValueAs(new Collection([new Literal('b'), 1])));
        self::assertTrue($collection->sameValueAs(new Collection([new Literal('a'), 1])));
    }

    public function testSameValueAsIsStrictAboutOrderAndTypes(): void
    {
        self::assertFalse((new Collection([1, 2]))->sameValueAs(new Collection([2, 1])));
        self::assertFalse((new Collection([1]))->sameValueAs(new Collection(['1'])));
    }

    public function testSubclassIsNotTheSameValueAsTheBaseCollection(): void
    {
        self::assertFalse((new Collection([1]))->sameValueAs(new CoreCollection([1])));
        self::assertFalse((new CoreCollection([1]))->sameValueAs(new Collection([1])));
        self::assertTrue((new CoreCollection([1]))->sameValueAs(new CoreCollection([1])));
    }

    public function testHasValueObjectItem(): void
    {
        $collection = new Collection([1, new Literal('a'), new Integer(5)]);

        self::assertTrue($collection->hasValueObjectItem(new Literal('a')));
        self::assertTrue($collection->hasValueObjectItem(new Integer(5)));
        self::assertFalse($collection->hasValueObjectItem(new Literal('b')));
        self::assertFalse($collection->hasValueObjectItem(new Integer(1)));
        self::assertFalse((new Collection([]))->hasValueObjectItem(new Literal('a')));
    }

    #[DataProvider('provideNeedlesThatAreNotValueObjects')]
    public function testHasValueObjectItemIsFalseForANeedleThatIsNotAValueObject(mixed $needle): void
    {
        // The previous release raised a TypeError when the collection held a value object.
        self::assertFalse((new Collection([new Literal('a'), 'a']))->hasValueObjectItem($needle));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function provideNeedlesThatAreNotValueObjects(): iterable
    {
        yield 'string' => ['a'];
        yield 'null' => [null];
        yield 'int' => [1];
        yield 'array' => [['a']];
        yield 'plain object' => [new \stdClass()];
    }

    public function testToArrayLeavesObjectsInsidePlainArraysAlone(): void
    {
        $literal = new Literal('a');

        self::assertSame([[$literal]], (new Collection([[$literal]]))->toArray());
    }

    public function testInheritedMutatorsChangeTheCollection(): void
    {
        $collection = new Collection([1]);
        $other = new Collection([1, 2]);

        self::assertFalse($collection->sameValueAs($other));

        $collection->add(2);

        self::assertTrue($collection->sameValueAs($other));
    }

    public function testToStringContainsTheObjectHash(): void
    {
        $collection = new Collection([]);

        self::assertStringStartsWith('Object ', (string) $collection);
        self::assertStringContainsString(\spl_object_hash($collection), (string) $collection);
    }
}
