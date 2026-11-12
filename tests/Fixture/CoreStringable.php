<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

/**
 * Not a value object; an object with a string form, used to feed exception messages.
 */
final class CoreStringable
{
    public function __toString(): string
    {
        return 'as text';
    }
}
