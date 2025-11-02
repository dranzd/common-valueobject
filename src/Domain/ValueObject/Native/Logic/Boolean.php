<?php

namespace Dranzd\Common\Domain\ValueObject\Native\Logic;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ScalarValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\ValueObject;

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
        return \sprintf('Boolean value object of %s', \strval($this->value));
    }

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

    public function toggle(): Boolean
    {
        return new static(!$this->isTrue());
    }
}
