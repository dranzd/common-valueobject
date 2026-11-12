<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Custom\Quantity;

use Dranzd\Common\Domain\ValueObject\ArrayValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Internal\ArrayKeys;
use Dranzd\Common\Domain\ValueObject\Native\Number\Real as Value;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as Unit;
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * A measured quantity: a real number and a free-form unit of measure (`1.5`, `'kg'`). The unit is not validated.
 *
 * Instances are immutable. Designed to be extended: `fromArray()` builds `static`.
 */
class Basic implements ValueObject, ArrayValueObjectTranslatorCapable
{
    use CompareSameValueTrait;
    use ToStringUsingObjectHashTrait;

    /** @var Value */
    protected $value;

    /** @var Unit */
    protected $unit;

    protected function __construct(Value $value, Unit $unit)
    {
        $this->value = $value;
        $this->unit = $unit;
    }

    /**
     * Zero with an empty-string unit.
     */
    public static function ofNoValue(): Basic
    {
        return static::fromArray([0, '']);
    }

    /**
     * Build from a positional list `[value, unit]` or from an array with the keys `value` and `unit`.
     *
     * The value is cast with `(float)` and the unit with `(string)`. Surplus elements and keys are ignored. The list
     * form is used whenever the array has an element `0`, even a null one; all its values, in order and whatever
     * their keys, are passed to the (late-static-bound) `fromNative()`. Keys are matched after camelCase conversion,
     * so `Value` works too; when two keys convert to the same name the later one wins.
     *
     * @param array<array-key, mixed> $array
     * @return static
     * @throws \InvalidArgumentException If an array with keys lacks `value` or `unit`, if a list has fewer than two
     *                                   elements, or if the value is NAN or infinite.
     */
    public static function fromArray(array $array): Basic
    {
        if (\array_key_exists(0, $array)) {
            return static::fromNative(...\array_values($array));
        }

        $data = ArrayKeys::toCamelCase($array);
        foreach (['value', 'unit'] as $key) {
            if (!\array_key_exists($key, $data)) {
                throw new \InvalidArgumentException(
                    \sprintf('Missing key "%s"; accepted keys: value, unit.', $key)
                );
            }
        }

        return static::fromNative($data['value'], $data['unit']);
    }

    /**
     * Same class, value and unit.
     */
    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    /**
     * @return array{value: float, unit: string}
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value->toNative(),
            'unit' => $this->unit->toNative(),
        ];
    }

    final public function getValue(): Value
    {
        return $this->value;
    }

    final public function getUnit(): Unit
    {
        return $this->unit;
    }

    /**
     * Factory behind {@see self::fromArray()}; override to customise construction.
     *
     * Arguments: `(float)` value, `(string)` unit. Extra arguments are ignored.
     *
     * @return static
     * @throws \InvalidArgumentException If fewer than two arguments are passed or the value is NAN or infinite.
     */
    protected static function fromNative(): Basic
    {
        if (\func_num_args() < 2) {
            throw new \InvalidArgumentException('Quantity needs two arguments: value and unit.');
        }

        // A non-numeric string such as 'abc' casts to 0.0, as it always has; only NAN and INF are refused.
        $value = (float) \func_get_arg(0);
        if (!\is_finite($value)) {
            throw new \InvalidArgumentException('Quantity value must be a finite number.');
        }

        $unit = (string) \func_get_arg(1);

        $value = Value::fromNative($value);
        $unit = Unit::fromNative($unit);

        return new static($value, $unit);
    }
}
