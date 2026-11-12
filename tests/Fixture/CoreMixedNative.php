<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * Compared through the trait by a `toNative()` that returns whatever it was given, to show the comparison is strict.
 */
final class CoreMixedNative implements ValueObject
{
    use CompareSameValueTrait;

    public function __construct(private readonly mixed $value)
    {
    }

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return 'mixed';
    }

    public function toNative(): mixed
    {
        return $this->value;
    }
}
