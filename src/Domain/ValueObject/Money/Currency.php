<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Money;

use Dranzd\Common\Domain\ValueObject\Native\String\Literal;

/**
 * @method static static USD()
 * @method static static EUR()
 * @method static static GBP()
 * @method static static JPY()
 * @method static static CNY()
 * @method static static PHP()
 * @method static static SGD()
 * @method static static AUD()
 * @method static static CAD()
 * @method static static CHF()
 * @method static static HKD()
 * @method static static NZD()
 * @method static static KRW()
 * @method static static THB()
 * @method static static INR()
 * @method static static MYR()
 * @method static static IDR()
 * @method static static VND()
 * @method static static TWD()
 * @method static static BRL()
 * @method static static MXN()
 * @method static static ZAR()
 * @method static static SEK()
 * @method static static NOK()
 * @method static static DKK()
 * @method static static AED()
 * @method static static SAR()
 *
 * A currency code as a string value object.
 *
 * `Currency::USD()` (any case, e.g. `usd()`) builds one of the codes in {@see CurrencyCode} and throws a
 * `\BadMethodCallException` for any other name. `fromString()`/`fromNative()` do NOT validate: any string,
 * including `''` or a code outside the enum, is accepted. Factories return `static`.
 */
class Currency extends Literal
{
    /**
     * Build from a known code.
     */
    final public static function fromCode(CurrencyCode $code): static
    {
        return new static($code->value);
    }

    /**
     * Build from any string; the code is not validated.
     */
    final public static function fromString(string $code): static
    {
        return new static($code);
    }

    final public function toString(): string
    {
        return $this->toNative();
    }

    /**
     * Same code, regardless of class (unlike `sameValueAs()`, which is class-sensitive).
     */
    final public function equals(Currency $other): bool
    {
        return $this->toNative() === $other->toNative();
    }

    /**
     * @param array<mixed> $arguments Ignored.
     * @throws \BadMethodCallException If the name is not a {@see CurrencyCode} case, ignoring case.
     */
    public static function __callStatic(string $name, array $arguments): static
    {
        $code = strtoupper($name);
        $enum = CurrencyCode::tryFrom($code);

        if ($enum === null) {
            throw new \BadMethodCallException(
                sprintf(
                    'Unknown currency code "%s". Use a valid CurrencyCode enum value.',
                    $code
                )
            );
        }

        return new static($enum->value);
    }
}
