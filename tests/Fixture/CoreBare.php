<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * Uses the comparison trait and has no way to be translated at all.
 */
final class CoreBare implements ValueObject
{
    use CompareSameValueTrait;

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return 'bare';
    }
}
