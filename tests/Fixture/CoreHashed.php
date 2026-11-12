<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * Uses the object-hash string conversion.
 */
final class CoreHashed implements ValueObject
{
    use ToStringUsingObjectHashTrait;

    public function sameValueAs(ValueObject $object): bool
    {
        return $this === $object;
    }
}
