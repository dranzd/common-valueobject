<?php

// strict_types is omitted on purpose: calls made from this class keep PHP's scalar coercion (e.g. fromNative('5')).

namespace Dranzd\Common\Domain\ValueObject\Native\Number;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ScalarValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * An integer number.
 */
class Integer implements ValueObject, ScalarValueObjectTranslatorCapable
{
    use CompareSameValueTrait;

    protected int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return \sprintf('Integer value object of %d', $this->value);
    }

    /**
     * Creates the value object from its native `int`, which is passed as the single argument.
     */
    public static function fromNative(): static
    {
        $value = func_get_arg(0);

        return new static($value);
    }

    public function toNative(): int
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return 0 === $this->value;
    }

    public function isNegative(): bool
    {
        return $this->value < 0;
    }

    /**
     * @return static
     */
    public function plus(Integer $number): Integer
    {
        return self::fromNative($this->value + $number->value);
    }

    /**
     * @return static
     */
    public function minus(Integer $number): Integer
    {
        return self::fromNative($this->value - $number->value);
    }

    /**
     * @return static
     */
    public function multiply(Integer $number): Integer
    {
        return self::fromNative($this->toNative() * $number->toNative());
    }

    public function isGreaterThan(Integer $number): bool
    {
        return $this->toNative() > $number->toNative();
    }

    public function isLessThan(Integer $number): bool
    {
        return $this->toNative() < $number->toNative();
    }

    public function isGreaterThanOrEqualTo(Integer $number): bool
    {
        return $this->toNative() >= $number->toNative();
    }

    public function isLessThanOrEqualTo(Integer $number): bool
    {
        return $this->toNative() <= $number->toNative();
    }

    public function isEqualTo(Integer $number): bool
    {
        return $this->toNative() === $number->toNative();
    }

    public function isNotEqualTo(Integer $number): bool
    {
        return $this->toNative() !== $number->toNative();
    }
}
