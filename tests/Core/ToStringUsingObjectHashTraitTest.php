<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Core;

use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreHashed;
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversTrait(ToStringUsingObjectHashTrait::class)]
#[UsesClass(CoreHashed::class)]
final class ToStringUsingObjectHashTraitTest extends TestCase
{
    public function testStringContainsTheObjectHash(): void
    {
        $object = new CoreHashed();
        $text = (string) $object;

        self::assertStringStartsWith('Object ', $text);
        self::assertStringContainsString(\spl_object_hash($object), $text);
    }

    public function testStringNamesTheTraitMethodAndTheHash(): void
    {
        $object = new CoreHashed();

        self::assertSame(
            \sprintf(
                'Object %s::__toString with object hash of %s',
                ToStringUsingObjectHashTrait::class,
                \spl_object_hash($object)
            ),
            (string) $object
        );
    }

    public function testDifferentObjectsHaveDifferentStrings(): void
    {
        $first = new CoreHashed();
        $second = new CoreHashed();

        self::assertNotSame((string) $first, (string) $second);
    }
}
