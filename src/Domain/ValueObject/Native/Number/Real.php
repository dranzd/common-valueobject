<?php

// strict_types is omitted on purpose: calls made from this class keep PHP's scalar coercion (e.g. fromNative('5')).

namespace Dranzd\Common\Domain\ValueObject\Native\Number;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ScalarValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * A real (floating point) number.
 */
class Real implements ValueObject, ScalarValueObjectTranslatorCapable
{
    use CompareSameValueTrait;

    protected float $value;

    public function __construct(float $value = 0.0)
    {
        $this->value = $value;
    }

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return \sprintf('Real value object of %s', $this->value);
    }

    /**
     * Creates the value object from its native `float`, which is passed as the single argument.
     */
    public static function fromNative(): static
    {
        $value = func_get_arg(0);

        return new static($value);
    }

    public function toNative(): float
    {
        return $this->value;
    }

    public static function withNoValue(): static
    {
        return new static(0.0);
    }

    public function isEmpty(): bool
    {
        return 0.0 === $this->value;
    }

    public function isNegative(): bool
    {
        return $this->value < 0;
    }

    public function plus(Real $reak): static
    {
        return self::fromNative($this->value + $reak->value);
    }

    public function minus(Real $reak): static
    {
        return self::fromNative($this->value - $reak->value);
    }

    public function multiply(Real $reak): static
    {
        return self::fromNative($this->toNative() * $reak->toNative());
    }

    public function isGreaterThan(Real $reak): bool
    {
        return $this->toNative() > $reak->toNative();
    }

    public function isLessThan(Real $reak): bool
    {
        return $this->toNative() < $reak->toNative();
    }

    public function isGreaterThanOrEqualTo(Real $reak): bool
    {
        return $this->toNative() >= $reak->toNative();
    }

    public function isLessThanOrEqualTo(Real $reak): bool
    {
        return $this->toNative() <= $reak->toNative();
    }

    public function isEqualTo(Real $reak): bool
    {
        return $this->toNative() === $reak->toNative();
    }

    public function isNotEqualTo(Real $reak): bool
    {
        return $this->toNative() !== $reak->toNative();
    }
}
