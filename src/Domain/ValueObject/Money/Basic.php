<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Money;

use Dranzd\Common\Domain\ValueObject\ArrayValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Internal\ArrayKeys;
use Dranzd\Common\Domain\ValueObject\Native\Number\Integer as Amount;
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * An amount of money: an integer number of minor units (cents, centavos) in a currency.
 *
 * Instances are immutable. Arithmetic and comparisons require both operands to have the same currency and
 * throw an {@see \InvalidArgumentException} otherwise. `Money\Basic` is designed to be extended: factories and
 * arithmetic return `static`, so a subclass gets subclass instances.
 */
class Basic implements ValueObject, ArrayValueObjectTranslatorCapable
{
    use CompareSameValueTrait;
    use ToStringUsingObjectHashTrait;

    /** @var Amount */
    protected $amount;

    /** @var Currency */
    protected $currency;

    protected function __construct(Amount $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Zero in no currency: amount 0 and an empty-string currency.
     */
    public static function ofNoValue(): Basic
    {
        return static::fromArray([0, '']);
    }

    /**
     * Build from a positional list `[amount, currency]` or from an array with the keys `amount` and `currency`.
     *
     * The amount is cast with `(int)`, so `'100'` becomes 100 and `100.9` is truncated to 100. Surplus elements and
     * keys are ignored. The list form is used whenever the array has an element `0`, even a null one; all its
     * values, in order and whatever their keys, are passed to the (late-static-bound) `fromNative()`, so a subclass
     * overriding that method sees them as arguments. Keys are matched after camelCase conversion, so `Amount` works
     * too; when two keys convert to the same name the later one wins.
     *
     * @param array<array-key, mixed> $array
     * @return static
     * @throws \InvalidArgumentException If an array with keys lacks `amount` or `currency`, or if a list has fewer
     *                                   than two elements.
     */
    public static function fromArray(array $array): Basic
    {
        if (\array_key_exists(0, $array)) {
            return static::fromNative(...\array_values($array));
        }

        $data = ArrayKeys::toCamelCase($array);
        foreach (['amount', 'currency'] as $key) {
            if (!\array_key_exists($key, $data)) {
                throw new \InvalidArgumentException(
                    \sprintf('Missing key "%s"; accepted keys: amount, currency.', $key)
                );
            }
        }

        return static::fromNative($data['amount'], $data['currency']);
    }

    /**
     * Same class, amount and currency. Class-sensitive: a subclass instance is never the same value as a base
     * instance. Use {@see self::equals()} to ignore the class.
     */
    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    /**
     * @return array{amount: int, currency: string}
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount->toNative(),
            'currency' => $this->currency->toNative(),
        ];
    }

    final public function getAmount(): Amount
    {
        return $this->amount;
    }

    final public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @throws \InvalidArgumentException If the currencies differ.
     * @throws \TypeError If the sum does not fit in an integer. Overflow below `PHP_INT_MIN` is only detected from
     *                    1025 units beyond it; closer than that the result silently becomes `PHP_INT_MIN`.
     */
    final public function add(Basic $other): Basic
    {
        $this->assertSameCurrency($other);

        return new static(
            $this->amount->plus($other->amount),
            $this->currency
        );
    }

    /**
     * @throws \InvalidArgumentException If the currencies differ.
     * @throws \TypeError If the difference does not fit in an integer. Overflow below `PHP_INT_MIN` is only
     *                    detected from 1025 units beyond it; closer than that the result silently becomes
     *                    `PHP_INT_MIN`.
     */
    final public function subtract(Basic $other): Basic
    {
        $this->assertSameCurrency($other);

        return new static(
            $this->amount->minus($other->amount),
            $this->currency
        );
    }

    /**
     * @throws \TypeError If the product does not fit in an integer. A product just below `PHP_INT_MIN` can silently
     *                    become `PHP_INT_MIN`, as for {@see self::subtract()}.
     */
    final public function multiply(int $factor): Basic
    {
        return new static(
            $this->amount->multiply(Amount::fromNative($factor)),
            $this->currency
        );
    }

    final public function isZero(): bool
    {
        return $this->amount->toNative() === 0;
    }

    final public function isPositive(): bool
    {
        return $this->amount->toNative() > 0;
    }

    final public function isNegative(): bool
    {
        return $this->amount->isNegative();
    }

    final public function isGreaterThan(Basic $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount->isGreaterThan($other->amount);
    }

    final public function isGreaterThanOrEqual(Basic $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount->isGreaterThanOrEqualTo($other->amount);
    }

    final public function isLessThan(Basic $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount->isLessThan($other->amount);
    }

    /**
     * Not final on purpose, unlike its siblings: it was added later and a subclass may already define it. A subclass
     * that does keeps working only if its signature is compatible: a parameter type of `Basic` or wider and the
     * return type `bool`. An override without the `bool` return type is a fatal error.
     *
     * @throws \InvalidArgumentException If the currencies differ.
     */
    public function isLessThanOrEqual(Basic $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount->isLessThanOrEqualTo($other->amount);
    }

    /**
     * Same amount and same currency code, regardless of the class of either operand or of either currency (compare
     * {@see self::sameValueAs()}). Never throws on a currency mismatch.
     */
    final public function equals(Basic $other): bool
    {
        return $this->amount->toNative() === $other->amount->toNative()
            && $this->currency->equals($other->currency);
    }

    private function assertSameCurrency(Basic $other): void
    {
        if (!$this->currency->equals($other->currency)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Currency mismatch: cannot operate on %s and %s',
                    $this->currency->toNative(),
                    $other->currency->toNative()
                )
            );
        }
    }

    /**
     * Factory behind {@see self::fromArray()}; override to customise construction.
     *
     * Arguments: `(int)` amount in minor units, `(string)` currency code. Both are cast, never validated. Extra
     * arguments are ignored.
     *
     * @return static
     * @throws \InvalidArgumentException If fewer than two arguments are passed.
     */
    protected static function fromNative(): Basic
    {
        if (\func_num_args() < 2) {
            throw new \InvalidArgumentException('Money needs two arguments: amount and currency.');
        }

        $amount = (int) \func_get_arg(0);
        $currency = (string) \func_get_arg(1);

        $amount = Amount::fromNative($amount);
        $currency = Currency::fromNative($currency);

        return new static($amount, $currency);
    }
}
