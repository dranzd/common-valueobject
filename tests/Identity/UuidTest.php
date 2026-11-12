<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Identity;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Identity\Uuid;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreOrderId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Uuid::class)]
#[UsesClass(Literal::class)]
#[UsesClass(CoreOrderId::class)]
#[UsesTrait(CompareSameValueTrait::class)]
final class UuidTest extends TestCase
{
    private const V4_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

    public function testGeneratesAVersion4UuidWhenNoValueIsGiven(): void
    {
        $first = new Uuid();
        $second = new Uuid(null);

        self::assertMatchesRegularExpression(self::V4_PATTERN, $first->toNative());
        self::assertNotSame($first->toNative(), $second->toNative());
    }

    public function testGenerateAsString(): void
    {
        $generated = Uuid::generateAsString();

        self::assertMatchesRegularExpression(self::V4_PATTERN, $generated);
        self::assertNotSame($generated, Uuid::generateAsString());
    }

    public function testFromNativeKeepsTheValueAsGiven(): void
    {
        $value = '550E8400-E29B-41D4-A716-446655440000';

        self::assertSame($value, Uuid::fromNative($value)->toNative());
        self::assertSame($value, (new Uuid($value))->toNative());
    }

    #[DataProvider('provideAcceptedValues')]
    public function testAcceptsWhatAssertUuidAccepts(string $value): void
    {
        self::assertSame($value, (new Uuid($value))->toNative());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideAcceptedValues(): iterable
    {
        yield 'lower case' => ['550e8400-e29b-41d4-a716-446655440000'];
        yield 'upper case' => ['550E8400-E29B-41D4-A716-446655440000'];
        yield 'nil uuid' => ['00000000-0000-0000-0000-000000000000'];
        yield 'version 1' => ['6ba7b810-9dad-11d1-80b4-00c04fd430c8'];
        yield 'braced' => ['{550e8400-e29b-41d4-a716-446655440000}'];
        yield 'urn' => ['urn:uuid:550e8400-e29b-41d4-a716-446655440000'];
    }

    /**
     * Pins how Assert::uuid() behaves: it strips "urn:", "uuid:", "{" and "}" from anywhere in the string before
     * matching, so these malformed forms are accepted and stored exactly as given.
     */
    #[DataProvider('provideMalformedButAcceptedValues')]
    public function testAcceptsMalformedFormsBecauseAssertStripsDecorationsAnywhere(string $value): void
    {
        self::assertSame($value, (new Uuid($value))->toNative());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideMalformedButAcceptedValues(): iterable
    {
        yield 'unbalanced opening brace' => ['{550e8400-e29b-41d4-a716-446655440000'];
        yield 'unbalanced closing brace' => ['550e8400-e29b-41d4-a716-446655440000}'];
        yield 'brace inside the value' => ['550e8400-e29b{-41d4-a716-446655440000'];
        yield 'uuid prefix' => ['uuid:550e8400-e29b-41d4-a716-446655440000'];
        yield 'repeated urn prefix' => ['urn:urn:550e8400-e29b-41d4-a716-446655440000'];
    }

    #[DataProvider('provideRejectedValues')]
    public function testRejectsInvalidStrings(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Uuid($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideRejectedValues(): iterable
    {
        yield 'text' => ['not-a-valid-uuid'];
        yield 'empty' => [''];
        yield 'number' => ['123'];
        yield 'no dashes' => ['550e8400e29b41d4a716446655440000'];
        yield 'trailing space' => ['550e8400-e29b-41d4-a716-446655440000 '];
        yield 'trailing newline' => ["550e8400-e29b-41d4-a716-446655440000\n"];
    }

    public function testFromNativeRejectsInvalidStrings(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Uuid::fromNative('not-a-valid-uuid');
    }

    public function testSameValueAs(): void
    {
        $value = '550e8400-e29b-41d4-a716-446655440000';

        self::assertTrue((new Uuid($value))->sameValueAs(new Uuid($value)));
        self::assertFalse((new Uuid($value))->sameValueAs(new Uuid()));
    }

    public function testSubclassFactoriesAndConstructorReturnTheSubclass(): void
    {
        $value = '550e8400-e29b-41d4-a716-446655440000';

        self::assertSame(CoreOrderId::class, (CoreOrderId::fromNative($value))::class);
        self::assertSame(CoreOrderId::class, (new CoreOrderId())::class);
        self::assertSame('order ' . $value, CoreOrderId::fromNative($value)->describe());
        self::assertMatchesRegularExpression(self::V4_PATTERN, CoreOrderId::generateAsString());
    }

    public function testSubclassIsNotTheSameValueAsUuidWithTheSameValue(): void
    {
        $value = '550e8400-e29b-41d4-a716-446655440000';

        self::assertFalse((new Uuid($value))->sameValueAs(new CoreOrderId($value)));
        self::assertFalse((new CoreOrderId($value))->sameValueAs(new Uuid($value)));
        self::assertTrue((new CoreOrderId($value))->sameValueAs(new CoreOrderId($value)));
    }
}
