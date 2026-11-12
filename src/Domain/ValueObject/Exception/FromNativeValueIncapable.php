<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Exception;

use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * Thrown when a value object cannot be created from a native value.
 */
final class FromNativeValueIncapable extends \DomainException
{
    final public static function fromObject(ValueObject $object): FromNativeValueIncapable
    {
        return new self(
            \sprintf(
                'Object of instance %s is incapable creating instance from native value.',
                \get_class($object)
            )
        );
    }
}
