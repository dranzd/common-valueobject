<?php

namespace Dranzd\Common\Domain\ValueObject\Tests\Domain\ValueObject\Native\String;

use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use PHPUnit\Framework\TestCase;

class LiteralTest extends TestCase
{
    public function testCanCreateFromString(): void
    {
        $literal = Literal::fromNative('Hello World');

        $this->assertInstanceOf(Literal::class, $literal);
        $this->assertSame('Hello World', $literal->toNative());
    }

    public function testIsEmptyReturnsTrueForEmptyString(): void
    {
        $literal = Literal::fromNative('');

        $this->assertTrue($literal->isEmpty());
    }

    public function testIsEmptyReturnsFalseForNonEmptyString(): void
    {
        $literal = Literal::fromNative('Hello');

        $this->assertFalse($literal->isEmpty());
    }

    public function testTwoLiteralsWithSameValueAreEqual(): void
    {
        $literal1 = Literal::fromNative('Hello');
        $literal2 = Literal::fromNative('Hello');

        $this->assertTrue($literal1->sameValueAs($literal2));
    }

    public function testTwoLiteralsWithDifferentValueAreNotEqual(): void
    {
        $literal1 = Literal::fromNative('Hello');
        $literal2 = Literal::fromNative('World');

        $this->assertFalse($literal1->sameValueAs($literal2));
    }
}
