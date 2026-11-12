<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Native\Logic;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Native\Logic\Boolean;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreFlag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Boolean::class)]
#[UsesTrait(CompareSameValueTrait::class)]
#[UsesClass(CoreFlag::class)]
final class BooleanTest extends TestCase
{
    public function testFactories(): void
    {
        self::assertTrue(Boolean::asTrue()->toNative());
        self::assertFalse(Boolean::asFalse()->toNative());
        self::assertTrue(Boolean::fromNative(true)->toNative());
        self::assertFalse(Boolean::fromNative(false)->toNative());
    }

    public function testFromNativeRejectsNull(): void
    {
        $this->expectException(\TypeError::class);

        Boolean::fromNative(null);
    }

    public function testIsTrueAndIsFalse(): void
    {
        self::assertTrue(Boolean::asTrue()->isTrue());
        self::assertFalse(Boolean::asTrue()->isFalse());
        self::assertTrue(Boolean::asFalse()->isFalse());
        self::assertFalse(Boolean::asFalse()->isTrue());
    }

    public function testToggle(): void
    {
        self::assertFalse(Boolean::asTrue()->toggle()->toNative());
        self::assertTrue(Boolean::asFalse()->toggle()->toNative());
    }

    public function testToStringPrintsTheValue(): void
    {
        self::assertSame('Boolean value object of true', (string) Boolean::asTrue());
        self::assertSame('Boolean value object of false', (string) Boolean::asFalse());
    }

    public function testSameValueAs(): void
    {
        self::assertTrue(Boolean::asTrue()->sameValueAs(new Boolean(true)));
        self::assertFalse(Boolean::asTrue()->sameValueAs(Boolean::asFalse()));
    }

    public function testFactoriesAndToggleReturnTheCalledClass(): void
    {
        self::assertSame(CoreFlag::class, (CoreFlag::asTrue())::class);
        self::assertSame(CoreFlag::class, (CoreFlag::asFalse())::class);
        self::assertSame(CoreFlag::class, (CoreFlag::fromNative(true))::class);
        self::assertSame(CoreFlag::class, (CoreFlag::asTrue()->toggle())::class);
        self::assertFalse(CoreFlag::asTrue()->toggle()->raw());
    }
}
