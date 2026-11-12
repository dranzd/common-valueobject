<?php

namespace Dranzd\Common\Domain\ValueObject;

/**
 * Implemented by value objects that wrap a single native (scalar) value.
 */
interface ScalarValueObjectTranslatorCapable
{
    /**
     * Creates the value object from its native value, passed as the single argument.
     *
     * @throws Exception\FromNativeValueIncapable
     */
    public static function fromNative(): ScalarValueObjectTranslatorCapable;

    /**
     * @return mixed the wrapped native value
     *
     * @throws Exception\ToNativeValueIncapable
     */
    public function toNative();
}
