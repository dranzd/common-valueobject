<?php

namespace Dranzd\Common\Domain\ValueObject;

/**
 * Contract of every value object: equality by value and a string representation.
 */
interface ValueObject
{
    /**
     * Tells whether the given object represents the same value as this one.
     */
    public function sameValueAs(ValueObject $object): bool;

    public function __toString(): string;
}
