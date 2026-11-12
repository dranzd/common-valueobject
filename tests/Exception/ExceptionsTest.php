<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Exception;

use Dranzd\Common\Domain\ValueObject\Exception\FromNativeValueIncapable;
use Dranzd\Common\Domain\ValueObject\Exception\InvalidNativeArgument;
use Dranzd\Common\Domain\ValueObject\Exception\ToNativeValueIncapable;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CorePlainObject;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreStringable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FromNativeValueIncapable::class)]
#[CoversClass(ToNativeValueIncapable::class)]
#[CoversClass(InvalidNativeArgument::class)]
#[UsesClass(Literal::class)]
final class ExceptionsTest extends TestCase
{
    public function testFromNativeValueIncapableNamesTheClass(): void
    {
        $exception = FromNativeValueIncapable::fromObject(new Literal('a'));

        self::assertSame(
            'Object of instance ' . Literal::class . ' is incapable creating instance from native value.',
            $exception->getMessage()
        );
    }

    public function testToNativeValueIncapableNamesTheClass(): void
    {
        $exception = ToNativeValueIncapable::fromObject(new Literal('a'));

        self::assertSame(
            'Object of instance ' . Literal::class . ' is incapable of translating it to native value.',
            $exception->getMessage()
        );
    }

    public function testInvalidNativeArgumentIsAnInvalidArgumentException(): void
    {
        $exception = new InvalidNativeArgument('abc', ['int', 'float']);

        self::assertSame(
            'Argument "abc" is invalid. Allowed types for argument are "int, float".',
            $exception->getMessage()
        );
        self::assertSame(0, $exception->getCode());
        self::assertNull($exception->getPrevious());
    }

    #[DataProvider('provideValues')]
    public function testInvalidNativeArgumentMessageIsSafeForAnyValue(mixed $value, string $expectedDescription): void
    {
        $exception = new InvalidNativeArgument($value, ['string']);

        self::assertSame(
            \sprintf('Argument "%s" is invalid. Allowed types for argument are "string".', $expectedDescription),
            $exception->getMessage()
        );
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function provideValues(): iterable
    {
        yield 'int' => [12, '12'];
        yield 'float' => [1.5, '1.5'];
        yield 'string' => ['text', 'text'];
        yield 'true' => [true, '1'];
        yield 'false prints as an empty string' => [false, ''];
        yield 'null prints as an empty string' => [null, ''];
        yield 'empty string' => ['', ''];
        yield 'stringable object prints its string value' => [new CoreStringable(), 'as text'];
        yield 'array is described by its type' => [[1, 2], 'array'];
        yield 'object without __toString is described by its class' => [
            new CorePlainObject(),
            CorePlainObject::class,
        ];
    }

    /**
     * @param class-string $class
     * @param class-string $parent
     */
    #[DataProvider('provideHierarchy')]
    public function testHierarchy(string $class, string $parent): void
    {
        self::assertSame($parent, \get_parent_class($class));
    }

    /**
     * @return iterable<string, array{class-string, class-string}>
     */
    public static function provideHierarchy(): iterable
    {
        yield 'from native incapable' => [FromNativeValueIncapable::class, \DomainException::class];
        yield 'to native incapable' => [ToNativeValueIncapable::class, \DomainException::class];
        yield 'invalid native argument' => [InvalidNativeArgument::class, \InvalidArgumentException::class];
    }

    public function testInvalidNativeArgumentWithoutAllowedTypes(): void
    {
        $exception = new InvalidNativeArgument('x', []);

        self::assertSame('Argument "x" is invalid. Allowed types for argument are "".', $exception->getMessage());
    }
}
