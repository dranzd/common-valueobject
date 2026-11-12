<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject;

/**
 * Value equality for value objects implementing {@see ArrayValueObjectTranslatorCapable} or
 * {@see ScalarValueObjectTranslatorCapable}.
 *
 * Two objects are the same value when they are of exactly the same class and their `toArray()` (strict array
 * comparison, so order and types matter) or `toNative()` results are identical. Anything else, including an object
 * whose `toNative()` throws {@see Exception\ToNativeValueIncapable}, is not equal.
 */
trait CompareSameValueTrait
{
    /**
     * @param mixed $valueObject
     */
    protected function checkSameValueAs($valueObject): bool
    {
        if (!\is_object($valueObject) || \get_class($this) !== \get_class($valueObject)) {
            return false;
        }

        if ($this instanceof ArrayValueObjectTranslatorCapable) {
            return $valueObject instanceof ArrayValueObjectTranslatorCapable
                && $this->toArray() === $valueObject->toArray();
        }

        $own = [$this, 'toNative'];
        $other = [$valueObject, 'toNative'];

        if (\is_callable($own) && \is_callable($other)) {
            try {
                return $own() === $other();
            } catch (Exception\ToNativeValueIncapable $e) {
                // not comparable by native value
            }
        }

        return false;
    }
}
