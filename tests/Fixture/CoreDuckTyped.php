<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * Uses the comparison trait and has a `toNative()` method without declaring the scalar interface.
 */
final class CoreDuckTyped implements ValueObject
{
    use CompareSameValueTrait;

    public function __construct(private readonly string $value)
    {
    }

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function toNative(): string
    {
        return $this->value;
    }
}
