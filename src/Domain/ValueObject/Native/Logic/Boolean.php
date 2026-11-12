<?php

// strict_types is omitted on purpose: calls made from this class keep PHP's scalar coercion (e.g. fromNative('5')).

namespace Dranzd\Common\Domain\ValueObject\Native\Logic;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ScalarValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * A boolean value.
 */
class Boolean implements ValueObject, ScalarValueObjectTranslatorCapable
{
    use CompareSameValueTrait;

    protected bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public static function asTrue(): static
    {
        return new static(true);
    }

    public static function asFalse(): static
    {
        return new static(false);
    }

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return \sprintf('Boolean value object of %s', $this->value ? 'true' : 'false');
    }

    /**
     * Creates the value object from its native `bool`, which is passed as the single argument.
     */
    public static function fromNative(): static
    {
        $value = func_get_arg(0);

        return new static($value);
    }

    public function toNative(): bool
    {
        return $this->value;
    }

    public function isTrue(): bool
    {
        return $this->toNative() === true;
    }

    public function isFalse(): bool
    {
        return $this->toNative() === false;
    }

    /**
     * @return static the opposite value, as an instance of the called class
     */
    public function toggle(): Boolean
    {
        return new static(!$this->isTrue());
    }
}
