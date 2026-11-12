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
        self::assertCount(5, \array_unique([
            $generated,
            Uuid::generateAsString(),
            Uuid::generateAsString(),
            Uuid::generateAsString(),
            Uuid::generateAsString(),
        ]));
        self::assertSame($generated, (new Uuid($generated))->toNative());
    }

    public function testFromNativeKeepsTheValueAsGiven(): void
    {
        $value = '550E8400-E29B-41D4-A716-446655440000';

        self::assertSame($value, Uuid::fromNative($value)->toNative());
        self::assertSame($value, (new Uuid($value))->toNative());
    }

    #[DataProvider('provideAcceptedValues')]
    public function testAcceptsEveryDocumentedForm(string $value): void
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
        yield 'uuid prefix' => ['uuid:550e8400-e29b-41d4-a716-446655440000'];
        yield 'upper case in every group' => ['ABCDEFAB-CDEF-ABCD-EFAB-CDEFABCDEFAB'];
        yield 'lower case in every group' => ['abcdefab-cdef-abcd-efab-cdefabcdefab'];
        yield 'mixed case' => ['550e8400-E29B-41d4-A716-446655440000'];
        yield 'upper case last group' => ['550e8400-e29b-41d4-a716-ABCDEFABCDEF'];
        yield 'max uuid' => ['ffffffff-ffff-ffff-ffff-ffffffffffff'];
        yield 'uuid prefix, braced' => ['uuid:{550e8400-e29b-41d4-a716-446655440000}'];
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
        yield 'unbalanced opening brace' => ['{550e8400-e29b-41d4-a716-446655440000'];
        yield 'unbalanced closing brace' => ['550e8400-e29b-41d4-a716-446655440000}'];
        yield 'brace inside the value' => ['550e8400-e29b{-41d4-a716-446655440000'];
        yield 'repeated urn prefix' => ['urn:urn:550e8400-e29b-41d4-a716-446655440000'];
        yield 'urn without uuid' => ['urn:550e8400-e29b-41d4-a716-446655440000'];
        yield 'urn with braces' => ['urn:uuid:{550e8400-e29b-41d4-a716-446655440000}'];
        yield 'upper-case urn prefix' => ['URN:UUID:550e8400-e29b-41d4-a716-446655440000'];
        yield 'upper-case uuid prefix' => ['UUID:550e8400-e29b-41d4-a716-446655440000'];
        yield 'repeated urn:uuid prefix' => ['urn:uuid:urn:uuid:550e8400-e29b-41d4-a716-446655440000'];
        yield 'repeated uuid prefix' => ['uuid:uuid:550e8400-e29b-41d4-a716-446655440000'];
        yield 'repeated uuid prefix, braced' => ['uuid:uuid:{550e8400-e29b-41d4-a716-446655440000}'];
        yield 'urn prefix without last colon' => ['urn:uuid550e8400-e29b-41d4-a716-446655440000'];
        yield 'uuid prefix without colon' => ['uuid550e8400-e29b-41d4-a716-446655440000'];
        yield 'urn prefix with other separators' => ['urn;uuid;550e8400-e29b-41d4-a716-446655440000'];
        yield 'doubled opening brace' => ['{{550e8400-e29b-41d4-a716-446655440000}'];
        yield 'doubled closing brace' => ['{550e8400-e29b-41d4-a716-446655440000}}'];
        yield 'parentheses' => ['(550e8400-e29b-41d4-a716-446655440000)'];
        yield 'group 1 long' => ['550e84000-e29b-41d4-a716-446655440000'];
        yield 'group 1 short' => ['550e840-e29b-41d4-a716-446655440000'];
        yield 'group 2 long' => ['550e8400-e29b0-41d4-a716-446655440000'];
        yield 'group 2 short' => ['550e8400-e29-41d4-a716-446655440000'];
        yield 'group 3 long' => ['550e8400-e29b-41d40-a716-446655440000'];
        yield 'group 3 short' => ['550e8400-e29b-41d-a716-446655440000'];
        yield 'group 4 long' => ['550e8400-e29b-41d4-a7160-446655440000'];
        yield 'group 4 short' => ['550e8400-e29b-41d4-a71-446655440000'];
        yield 'group 5 long' => ['550e8400-e29b-41d4-a716-4466554400000'];
        yield 'non-hex in group 1' => ['g50e8400-e29b-41d4-a716-446655440000'];
        yield 'underscore in group 1' => ['_50e8400-e29b-41d4-a716-446655440000'];
        yield 'non-hex in group 2' => ['550e8400-g29b-41d4-a716-446655440000'];
        yield 'non-hex in group 3' => ['550e8400-e29b-g1d4-a716-446655440000'];
        yield 'non-hex in group 4' => ['550e8400-e29b-41d4-g716-446655440000'];
        yield 'dash 1 missing' => ['550e8400e29b-41d4-a716-446655440000'];
        yield 'dash 2 missing' => ['550e8400-e29b41d4-a716-446655440000'];
        yield 'dash 3 missing' => ['550e8400-e29b-41d4a716-446655440000'];
        yield 'dash 4 missing' => ['550e8400-e29b-41d4-a716446655440000'];
        yield 'underscores for dashes' => ['550e8400_e29b_41d4_a716_446655440000'];
        yield 'dots for dashes' => ['550e8400.e29b.41d4.a716.446655440000'];
        yield 'dash 1 replaced' => ['550e8400_e29b-41d4-a716-446655440000'];
        yield 'dash 2 replaced' => ['550e8400-e29b_41d4-a716-446655440000'];
        yield 'dash 3 replaced' => ['550e8400-e29b-41d4_a716-446655440000'];
        yield 'dash 4 replaced' => ['550e8400-e29b-41d4-a716_446655440000'];
        yield 'underscore in group 2' => ['550e8400-_29b-41d4-a716-446655440000'];
        yield 'underscore in group 3' => ['550e8400-e29b-_1d4-a716-446655440000'];
        yield 'underscore in group 4' => ['550e8400-e29b-41d4-_716-446655440000'];
        yield 'underscore in group 5' => ['550e8400-e29b-41d4-a716-_46655440000'];
        yield 'braces with urn inside' => ['{urn:uuid:550e8400-e29b-41d4-a716-446655440000}'];
        yield 'braces with uuid prefix inside' => ['{uuid:550e8400-e29b-41d4-a716-446655440000}'];
        yield 'leading space' => [' 550e8400-e29b-41d4-a716-446655440000'];
        yield 'group 5 short' => ['550e8400-e29b-41d4-a716-44665544000'];
        yield 'non-hex in group 5' => ['550e8400-e29b-41d4-a716-44665544000g'];
    }

    public function testRejectionNamesTheValue(): void
    {
        try {
            new Uuid('{nope}');
            self::fail('Expected an exception.');
        } catch (\InvalidArgumentException $exception) {
            self::assertSame(\Webmozart\Assert\InvalidArgumentException::class, $exception::class);
            self::assertSame('Value "{nope}" is not a valid UUID.', $exception->getMessage());
        }
    }

    public function testRejectionMessageForPlainText(): void
    {
        try {
            new Uuid('nope');
            self::fail('Expected an exception.');
        } catch (\InvalidArgumentException $exception) {
            self::assertSame(\Webmozart\Assert\InvalidArgumentException::class, $exception::class);
            self::assertSame('Value "nope" is not a valid UUID.', $exception->getMessage());
        }
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
