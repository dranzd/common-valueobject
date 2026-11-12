<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Native\String;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreSku;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Literal::class)]
#[UsesTrait(CompareSameValueTrait::class)]
#[UsesClass(CoreSku::class)]
final class LiteralTest extends TestCase
{
    public function testDefaultsToEmptyString(): void
    {
        self::assertSame('', (new Literal())->toNative());
    }

    public function testFromNativeAndToNative(): void
    {
        $literal = Literal::fromNative('Hello World');

        self::assertSame('Hello World', $literal->toNative());
    }

    public function testIsEmpty(): void
    {
        self::assertTrue(Literal::fromNative('')->isEmpty());
        self::assertFalse(Literal::fromNative('0')->isEmpty());
    }

    public function testToString(): void
    {
        self::assertSame('Literal value object of abc', (string) new Literal('abc'));
    }

    public function testSameValueAs(): void
    {
        self::assertTrue((new Literal('a'))->sameValueAs(new Literal('a')));
        self::assertFalse((new Literal('a'))->sameValueAs(new Literal('b')));
    }

    public function testFromNativeReturnsTheCalledClass(): void
    {
        $sku = CoreSku::fromNative('ab-1');

        self::assertSame(CoreSku::class, ($sku)::class);
        self::assertSame('AB-1', $sku->upper());
    }

    public function testFromNativeWithoutArgumentFails(): void
    {
        $this->expectException(\ValueError::class);

        Literal::fromNative();
    }

    public function testFromNativeCoercesScalarsBecauseStrictTypesAreOffOnPurpose(): void
    {
        self::assertSame('5', Literal::fromNative(5)->toNative());
        self::assertSame('1.5', Literal::fromNative(1.5)->toNative());
        self::assertSame('1', Literal::fromNative(true)->toNative());
        self::assertSame('', Literal::fromNative(false)->toNative());
    }

    public function testFromNativeRejectsNull(): void
    {
        $this->expectException(\TypeError::class);

        Literal::fromNative(null);
    }

    public function testWhitespaceIsNotEmpty(): void
    {
        self::assertFalse(Literal::fromNative(' ')->isEmpty());
    }
}
