<?php

namespace Dranzd\Common\Domain\ValueObject;

/**
 * Implemented by value objects that are translated from and to a plain array.
 */
interface ArrayValueObjectTranslatorCapable
{
    /**
     * @param array<string|int, mixed> $array
     */
    public static function fromArray(array $array): ValueObject;

    /**
     * @return array<string|int, mixed>
     */
    public function toArray(): array;
}
