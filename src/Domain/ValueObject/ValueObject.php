<?php

namespace Dranzd\Common\Domain\ValueObject;

interface ValueObject
{
    public function sameValueAs(ValueObject $object): bool;

    public function __toString(): string;
}
