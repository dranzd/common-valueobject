# Money

Namespace `Dranzd\Common\Domain\ValueObject\Money`. Three types: `Basic` (an amount in a currency), `Currency`
(a currency code) and `CurrencyCode` (the enum of codes `Currency` knows by name).

## `Money\Basic`

An immutable amount of money. The amount is an **integer count of minor units** (cents, centavos): `1999` USD is
19.99 USD. It is an `ArrayValueObjectTranslatorCapable`, so it is built with `fromArray()` and read with `toArray()`.
The class is designed to be extended; the constructor and `fromNative()` are `protected`.

```php
public static function fromArray(array $array): Basic;     // returns static
public static function ofNoValue(): Basic;                  // amount 0, currency ''
public function toArray(): array;                           // ['amount' => int, 'currency' => string]
public function sameValueAs(ValueObject $object): bool;

final public function getAmount(): Native\Number\Integer;
final public function getCurrency(): Currency;
final public function add(Basic $other): Basic;
final public function subtract(Basic $other): Basic;
final public function multiply(int $factor): Basic;
final public function isZero(): bool;
final public function isPositive(): bool;
final public function isNegative(): bool;
final public function isGreaterThan(Basic $other): bool;
final public function isGreaterThanOrEqual(Basic $other): bool;
final public function isLessThan(Basic $other): bool;
public function isLessThanOrEqual(Basic $other): bool;      // not final, see below
final public function equals(Basic $other): bool;

protected static function fromNative(): Basic;              // (amount, currency), override point
```

### Input

`fromArray()` takes either form:

- a list `[amount, currency]`, used whenever the array has an element `0`, even a null one (null casts to `0`). All
  values of the array are passed, in order and whatever their keys, to the late-static-bound `fromNative()`, so a
  subclass that overrides it receives them as arguments. Surplus elements are ignored.
- an array with the keys `amount` and `currency`. Surplus keys are ignored. Keys are converted to camelCase first, so
  `Amount` and `Currency` work too, but `AMOUNT` does not (it becomes `aMOUNT`). When two keys convert to the same name
  the later one wins: `['amount' => 100, 'currency' => 'USD', 'Currency' => 'EUR']` is 100 EUR.

Failures, all `\InvalidArgumentException`:

- a list with fewer than two elements: `Money needs two arguments: amount and currency.`;
- an array with keys that lacks one of them: `Missing key "currency"; accepted keys: amount, currency.`

Nothing else is rejected by the library. A part that cannot be cast is not reported as an `\InvalidArgumentException`:
an array as the currency becomes the string `Array` with a PHP warning, and an object without `__toString()` raises an
`\Error`.

### Coercion

The amount is cast with `(int)` and the currency with `(string)`; neither is validated.

- `'100'` becomes `100`, and a non-numeric string becomes `0`.
- Fractional input is **truncated, not rounded**: `100.9` becomes `100`. Convert to minor units yourself.
- The currency is any string, including `''` and codes outside `CurrencyCode`.

### Arithmetic and comparison

`add`, `subtract` and the four comparisons require equal currency codes and throw `\InvalidArgumentException` with a
message starting `Currency mismatch` otherwise. Codes are compared case-sensitively and by value, so a `Currency`
subclass holding the same code matches. Results of `add`, `subtract` and `multiply` are `static` instances with the
left operand's currency.

Overflow: a result above `PHP_INT_MAX` throws a `\TypeError`. Below `PHP_INT_MIN` it throws only when the result is
1025 or more units beyond it: `subtract` of up to 1024 units from `PHP_INT_MIN` (or `add` of a negative amount that
size) silently returns `PHP_INT_MIN`, because the float result rounds back onto it. `multiply` can do the same for a
product just below `PHP_INT_MIN`. Do not rely on money arithmetic at the extremes of the integer range.

### `sameValueAs()` versus `equals()`

| | compares class | compares amount and currency code | on currency mismatch |
|---|---|---|---|
| `sameValueAs()` | yes | yes | returns false |
| `equals()` | no | yes | returns false |

A `Money\Basic` and a subclass instance with the same amount and currency are `equals()` but not `sameValueAs()`.
`equals()` also ignores the class of the currency objects themselves.

### `isLessThanOrEqual()`

Not `final`, unlike the other comparisons. A subclass that declares a method of that name is compatible only if its
signature is: a parameter type of `Basic` or wider and the return type `bool`. An override without the `bool` return
type is a fatal error.

### Example

```php
use Dranzd\Common\Domain\ValueObject\Money\Basic as Money;

$a = Money::fromArray(['amount' => 1999, 'currency' => 'USD']);
$b = Money::fromArray([500, 'USD']);

echo json_encode($a->add($b)->toArray());       // {"amount":2499,"currency":"USD"}
echo json_encode($a->subtract($b)->toArray());  // {"amount":1499,"currency":"USD"}
echo json_encode($b->multiply(3)->toArray());   // {"amount":1500,"currency":"USD"}
var_dump($b->isLessThanOrEqual($a));            // bool(true)
var_dump(Money::fromArray([100.9, 'USD'])->getAmount()->toNative()); // int(100)
echo json_encode(Money::fromArray(['amount' => 100, 'currency' => 'USD', 'Currency' => 'EUR'])->toArray());
                                                // {"amount":100,"currency":"EUR"}

try {
    $a->add(Money::fromArray([1, 'EUR']));
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage();                      // Currency mismatch: cannot operate on USD and EUR
}
try {
    Money::fromArray(['amount' => 1]);
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage();                      // Missing key "currency"; accepted keys: amount, currency.
}
```

### Subclassing

```php
final class Price extends Money
{
}

$price = Price::fromArray([100, 'USD']);   // Price
$total = $price->add($price);              // Price, 200 USD
```

`protected $amount` and `$currency` stay untyped so existing subclasses can redeclare them.

## `Money\Currency`

Extends `Native\String\Literal`. A currency code as a string value.

```php
final public static function fromCode(CurrencyCode $code): static;
final public static function fromString(string $code): static;
final public function toString(): string;
final public function equals(Currency $other): bool;
public static function __callStatic(string $name, array $arguments): static;
// inherited from Literal: fromNative(), toNative(), isEmpty(), sameValueAs()
```

- `Currency::USD()` builds a code from the `CurrencyCode` enum. The name is matched case-insensitively
  (`Currency::usd()` works). An unknown name throws `\BadMethodCallException`
  (`Unknown currency code "XYZ". Use a valid CurrencyCode enum value.`).
- `fromString()` and the inherited `fromNative()` do not validate: any string is accepted, including `''` and
  `'xyz'`. `Money\Basic` builds its currency through `fromNative()`.
- `equals()` compares the code only, ignoring the class; `sameValueAs()` also requires the same class.
- All factories return `static`, so a subclass gets subclass instances.

```php
echo Currency::usd()->toString();                // USD
echo Currency::fromString('xyz')->toString();    // xyz
try {
    Currency::XYZ();
} catch (\BadMethodCallException $e) {
    echo $e->getMessage();                       // Unknown currency code "XYZ". Use a valid CurrencyCode enum value.
}
```

## `Money\CurrencyCode`

A string-backed enum whose case name equals its value. Current cases, in order:

`USD EUR GBP JPY CNY PHP SGD AUD CAD CHF HKD NZD KRW THB INR MYR IDR VND TWD BRL MXN ZAR SEK NOK DKK AED SAR`

`CurrencyCode::from('XYZ')` throws `\ValueError`;
`tryFrom()` returns null. Lookup is case-sensitive.
