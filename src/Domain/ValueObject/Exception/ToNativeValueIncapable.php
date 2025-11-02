<?php

namespace Dranzd\Common\Domain\ValueObject\Exception;

use Dranzd\Common\Domain\ValueObject\ValueObject;

final class ToNativeValueIncapable extends \DomainException
{
    final public static function fromObject(ValueObject $object)
    {
        return new static(
            sprintf(
                'Object of instance %s is incapable of translating it to native value.',
                get_class($object)
            )
        );
    }
}
