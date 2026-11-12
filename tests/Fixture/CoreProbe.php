<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * Exposes the trait's protected comparison so it can be fed values that are not value objects.
 */
final class CoreProbe implements ValueObject
{
    use CompareSameValueTrait;

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function probe(mixed $other): bool
    {
        return $this->checkSameValueAs($other);
    }

    public function __toString(): string
    {
        return 'probe';
    }

    public function toNative(): string
    {
        return 'probe';
    }
}
