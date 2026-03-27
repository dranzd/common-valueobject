<?php

namespace Dranzd\Common\Domain\ValueObject\Money;

use Dranzd\Common\Domain\ValueObject\ArrayValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Native\Number\Integer as Amount;
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;
use Dranzd\Common\Utils\ArrayUtil;

class Basic implements ValueObject, ArrayValueObjectTranslatorCapable
{
    use CompareSameValueTrait;
    use ToStringUsingObjectHashTrait;

    /**
     * @var Amount
     */
    protected $amount;

    /**
     * @var Currency
     */
    protected $currency;

    protected function __construct(Amount $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function ofNoValue(): Basic
    {
        return static::fromArray([0, '']);
    }

    public static function fromArray(array $array): Basic
    {
        if (isset($array[0])) {
            return \call_user_func_array(['static', 'fromNative'], $array);
        }

        \extract(ArrayUtil::keyToCamelCase($array));

        return static::fromNative($amount, $currency);
    }

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

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

    final public function add(Basic $other): Basic
    {
        $this->assertSameCurrency($other);

        return new static(
            $this->amount->plus($other->amount),
            $this->currency
        );
    }

    final public function subtract(Basic $other): Basic
    {
        $this->assertSameCurrency($other);

        return new static(
            $this->amount->minus($other->amount),
            $this->currency
        );
    }

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

    final public function equals(Basic $other): bool
    {
        return $this->amount->toNative() === $other->amount->toNative()
            && $this->currency->sameValueAs($other->currency);
    }

    private function assertSameCurrency(Basic $other): void
    {
        if (!$this->currency->sameValueAs($other->currency)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Currency mismatch: cannot operate on %s and %s',
                    $this->currency->toNative(),
                    $other->currency->toNative()
                )
            );
        }
    }

    protected static function fromNative(): Basic
    {
        $amount = (int) \func_get_arg(0);
        $currency = (string) \func_get_arg(1);

        $amount = Amount::fromNative($amount);
        $currency = Currency::fromNative($currency);

        return new static($amount, $currency);
    }
}
