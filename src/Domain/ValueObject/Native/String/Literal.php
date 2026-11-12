<?php

// strict_types is omitted on purpose: calls made from this class keep PHP's scalar coercion (e.g. fromNative('5')).

namespace Dranzd\Common\Domain\ValueObject\Native\String;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ScalarValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * A string.
 */
class Literal implements ValueObject, ScalarValueObjectTranslatorCapable
{
    use CompareSameValueTrait;

    protected string $value;

    public function __construct(string $value = '')
    {
        $this->value = $value;
    }

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return \sprintf('Literal value object of %s', $this->value);
    }

    /**
     * Creates the value object from its native `string`, which is passed as the single argument.
     */
    public static function fromNative(): static
    {
        $value = func_get_arg(0);

        return new static($value);
    }

    public function toNative(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return '' === $this->value;
    }
}
