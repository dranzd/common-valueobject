<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject;

/**
 * Default `__toString()` for value objects that have no meaningful textual form: `Object <method> with object hash
 * of <hash>`, where `<method>` is always the trait's own method name.
 */
trait ToStringUsingObjectHashTrait
{
    public function __toString(): string
    {
        return \sprintf('Object %s with object hash of %s', __METHOD__, \spl_object_hash($this));
    }
}
