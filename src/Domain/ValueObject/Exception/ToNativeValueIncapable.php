<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Exception;

use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * Thrown when a value object cannot be translated to a native value.
 */
final class ToNativeValueIncapable extends \DomainException
{
    final public static function fromObject(ValueObject $object): ToNativeValueIncapable
    {
        return new self(
            \sprintf(
                'Object of instance %s is incapable of translating it to native value.',
                \get_class($object)
            )
        );
    }
}
