# Core reference

Namespace `Dranzd\Common\Domain\ValueObject`. This page covers the contracts, the shared traits, the exceptions, the
primitive value objects (`Boolean`, `Integer`, `Real`, `Literal`), the date and time objects, `Uuid` and `Collection`.

## Contracts

```php
interface ValueObject
{
    public function sameValueAs(ValueObject $object): bool;
    public function __toString(): string;
}

interface ScalarValueObjectTranslatorCapable
{
    public static function fromNative(): ScalarValueObjectTranslatorCapable; // takes one argument, see below
    public function toNative();                                              // returns the wrapped native value
}

interface ArrayValueObjectTranslatorCapable
{
    public static function fromArray(array $array): ValueObject;
    public function toArray(): array;
}
```

`fromNative()` is declared without parameters and reads its single argument with `func_get_arg(0)`. Callers pass the
native value (`Integer::fromNative(5)`); calling it with no argument raises an error from `func_get_arg`.

## Traits

### CompareSameValueTrait

Provides `protected function checkSameValueAs($valueObject): bool`, used to implement `sameValueAs()`.

Rules, in order:

1. Not an object, or not exactly the same class as `$this` (a subclass is not equal to its parent): `false`.
2. `$this` implements `ArrayValueObjectTranslatorCapable`: `toArray() === toArray()` (strict, so key order and item
   types matter).
3. Otherwise, if both objects have a callable `toNative()`: `toNative() === toNative()`. A thrown
   `ToNativeValueIncapable` gives `false`.
4. Anything else: `false`.

### ToStringUsingObjectHashTrait

`__toString()` returning `Object <method> with object hash of <spl_object_hash>`. `<method>` is always the trait's
own method name (`Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait::__toString`), not the using class.

## Exceptions

| Class | Extends | Notes |
|---|---|---|
| `Exception\FromNativeValueIncapable` | `\DomainException` | `fromObject(ValueObject $object): self`; message names the object's class |
| `Exception\ToNativeValueIncapable` | `\DomainException` | `fromObject(ValueObject $object): self`; message names the object's class |
| `Exception\InvalidNativeArgument` | `\InvalidArgumentException` | `__construct($value, array $allowedTypes)` |

All three are `final`. `InvalidNativeArgument` prints the value with a plain string cast when it is a scalar, `null` or
a `\Stringable` object, so `true` prints as `1` and `false` and `null` print as an empty string. Arrays and objects
that cannot be turned into a string are described by their type (`get_debug_type()`), so it can be built from any
value:

```php
(new InvalidNativeArgument([1], ['string']))->getMessage();
// Argument "array" is invalid. Allowed types for argument are "string".
(new InvalidNativeArgument(false, ['string']))->getMessage();
// Argument "" is invalid. Allowed types for argument are "string".
```

## Native value objects

All four implement `ValueObject` and `ScalarValueObjectTranslatorCapable`, use `CompareSameValueTrait`, keep their
value in a `protected` property `$value` and are meant to be extended. Factories use `new static`, so a subclass gets
subclass instances back from `fromNative()`.

These four files (and `Uuid`) do not declare `strict_types`, so `fromNative()` and the arithmetic helpers keep PHP's
scalar coercion: `Integer::fromNative('5')` gives 5, `Real::fromNative('1.5')` gives 1.5 and
`Literal::fromNative(5)` gives `'5'`. Values that cannot be coerced (`null`, `'five'` for an integer) raise a
`TypeError`. The constructors are coerced the same way when the calling file is not itself in strict mode.

### Native\Logic\Boolean

```php
public function __construct(bool $value);
public static function asTrue(): static;
public static function asFalse(): static;
public static function fromNative(): static;      // fromNative(bool $value)
public function toNative(): bool;
public function isTrue(): bool;
public function isFalse(): bool;
public function toggle(): Boolean;                // an instance of the called class
public function sameValueAs(ValueObject $object): bool;
public function __toString(): string;             // "Boolean value object of true" / "... of false"
```

### Native\Number\Integer

```php
public function __construct(int $value);
public static function fromNative(): static;      // fromNative(int $value)
public function toNative(): int;
public function isEmpty(): bool;                  // value is 0
public function isNegative(): bool;
public function plus(Integer $number): Integer;     // an instance of the called class
public function minus(Integer $number): Integer;    // an instance of the called class
public function multiply(Integer $number): Integer; // an instance of the called class
public function isGreaterThan(Integer $number): bool;
public function isLessThan(Integer $number): bool;
public function isGreaterThanOrEqualTo(Integer $number): bool;
public function isLessThanOrEqualTo(Integer $number): bool;
public function isEqualTo(Integer $number): bool;
public function isNotEqualTo(Integer $number): bool;
```

`__toString()` gives `Integer value object of 7`. The result of `plus`, `minus` and `multiply` goes through
`fromNative()`, so an overflowing result (it becomes a float) raises a `TypeError`.

### Native\Number\Real

Same shape as `Integer` with `float` values, plus `public static function withNoValue(): static` (zero). The
constructor defaults to `0.0`. `plus`, `minus` and `multiply` return `static`. `isEmpty()` is true only for zero
(`0.0`, `-0.0`). `__toString()` gives `Real value object of 1.5`. Comparison is by strict `===` on the float, so a
`Real` holding `NAN` is not `sameValueAs()` itself and `isEqualTo()` is false for it too.

### Native\String\Literal

```php
public function __construct(string $value = '');
public static function fromNative(): static;      // fromNative(string $value)
public function toNative(): string;
public function isEmpty(): bool;                  // value is ''
```

`__toString()` gives `Literal value object of <value>`, not the bare value; use `toNative()` for the string.

## Dates and times

`DateTime\Date` (`Y-m-d`), `DateTime\DateTime` (`Y-m-d H:i:s`) and `DateTime\Time` (`H:i:s`) extend `Literal` and keep
the value as a string.

```php
public function __construct(string|null $value = null);
final public static function isFormatValid($datetime): bool;
```

- `null` means now, taken with `date()` in the default timezone.
- A value is valid when `\DateTime::createFromFormat()` accepts it with the format. That is PHP's lenient parser:
  overflowing values such as `2024-02-30`, `24:00:00` or `2024-02-03 25:00:00`, unpadded months and days such as
  `2024-1-5`, and `0000-00-00` are accepted and stored exactly as given. Empty strings, text that is not a date,
  trailing or leading characters, and a value in another one of the three formats are rejected. The answer does not
  depend on the default timezone.
- `isFormatValid()` returns `false` for anything that is not a string.
- The constructor throws `Webmozart\Assert\InvalidArgumentException` (a subclass of `\InvalidArgumentException`) for
  a value that is not accepted, with a message such as `Expected a date in the format "Y-m-d", got "garbage".`. The
  message is built once, so a value containing `%` appears in it unchanged.

## Identity\Uuid

Extends `Literal`; intended to be subclassed into typed identifiers.

```php
public function __construct(string|null $value = null);
public static function generateAsString(): string;
```

`null` generates a version 4 UUID. Any other value must be a UUID in one of these forms, and is kept exactly as given:

| Form | Example |
|---|---|
| Plain (the nil UUID included) | `550e8400-e29b-41d4-a716-446655440000` |
| Braced | `{550e8400-e29b-41d4-a716-446655440000}` |
| URN | `urn:uuid:550e8400-e29b-41d4-a716-446655440000` |
| `uuid:` prefix, plain | `uuid:550e8400-e29b-41d4-a716-446655440000` |
| `uuid:` prefix, braced | `uuid:{550e8400-e29b-41d4-a716-446655440000}` |

Hex digits may be in either case; the prefixes must be lower case.

Anything else throws `Webmozart\Assert\InvalidArgumentException` (an `\InvalidArgumentException`) with the message
`Value "<input>" is not a valid UUID.` That covers text that is not a UUID, the empty string, a value without dashes,
surrounding whitespace or a trailing newline, and decorations in any other arrangement: an unbalanced or doubled
brace, braces combined with `urn:uuid:` (`urn:uuid:{...}`), a prefix inside the braces (`{urn:uuid:...}`), and a
repeated, partial or upper-case prefix. The check is made by `Uuid` itself, so it is the same whichever version of
`webmozart/assert` is installed.

`fromNative()` and `generateAsString()` are inherited / use `new static`, so a subclass gets its own type back.
`sameValueAs()` is false between a `Uuid` and a subclass holding the same string.

## Collection\Collection

Extends Doctrine's `ArrayCollection` and implements `ValueObject` and `ArrayValueObjectTranslatorCapable`. Documented
with `@template TKey of array-key = array-key`, `@template T = mixed` and `@extends ArrayCollection<TKey, T>`; the
defaults keep a subclass such as `class Sources extends Collection {}` valid for PHPStan without any annotation.

Unlike the other value objects, `Collection` inherits Doctrine's mutators (`add()`, `set()`, `remove()`, `clear()`
and the rest), so it is not immutable and its `sameValueAs()` answer changes when it is modified.

```php
public static function fromArray(array $array): Collection; // always a plain Collection
public function toArray(): array;
public function hasValueObjectItem($anItem): bool;
public function sameValueAs(ValueObject $object): bool;
public function __toString(): string;
```

- `fromArray()` always builds the base `Collection`, whichever class it is called on, so a subclass with its own
  constructor is never invoked. Subclasses should use their own constructor or Doctrine's `createFrom()`.
- `toArray()` converts items: an object with a `toArray()` method is replaced by its result, otherwise one with
  `toNative()` by its result, otherwise it is kept. Nested collections therefore convert recursively; objects inside
  plain arrays are left as they are.
- `hasValueObjectItem($anItem)` is true when some item is a `ValueObject` that is `sameValueAs($anItem)`. It is false
  when `$anItem` is not a `ValueObject`.
- `sameValueAs()` compares `toArray()` strictly and requires the same class.
- `__toString()` is `Object <method> with object hash of <hash>`.

## Example

```php
final class OrderId extends Uuid {}
final class Sku extends Literal {}

$id = OrderId::fromNative('550e8400-e29b-41d4-a716-446655440000');
echo get_class($id), "\n";                                    // OrderId
var_dump($id->sameValueAs(new Uuid('550e8400-e29b-41d4-a716-446655440000'))); // bool(false)
var_dump($id->sameValueAs(OrderId::fromNative('550e8400-e29b-41d4-a716-446655440000'))); // bool(true)
echo new Sku('A-1'), "\n";                                    // Literal value object of A-1
echo Boolean::asFalse(), "\n";                                // Boolean value object of false
echo (new Integer(2))->plus(new Integer(3))->toNative(), "\n"; // 5

echo (new Date('2024-02-30'))->toNative(), "\n";             // 2024-02-30
try {
    new Date('100%');
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage(), "\n";                              // Expected a date in the format "Y-m-d", got "100%".
}
var_dump(Date::isFormatValid('2024-02-29'), Date::isFormatValid('garbage')); // bool(true) bool(false)
echo Integer::fromNative('5')->toNative(), "\n";             // 5

$c = Collection::fromArray([new Literal('a'), new Collection([1, new Integer(2)])]);
echo json_encode($c->toArray()), "\n";                        // ["a",[1,2]]
var_dump($c->hasValueObjectItem(new Literal('a')));           // bool(true)
var_dump($c->hasValueObjectItem('a'));                        // bool(false)
```
