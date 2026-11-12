# Common Value Object

[![CI](https://github.com/dranzd/common-valueobject/actions/workflows/ci.yml/badge.svg)](https://github.com/dranzd/common-valueobject/actions/workflows/ci.yml)
[![Latest version](https://img.shields.io/packagist/v/dranzd/common-valueobject.svg)](https://packagist.org/packages/dranzd/common-valueobject)
[![PHP version](https://img.shields.io/packagist/php-v/dranzd/common-valueobject.svg)](composer.json)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

Building blocks for value objects in Domain-Driven Design: a small contract (`ValueObject`), base
classes to extend for your own typed identifiers and primitives, and a few ready-made types for
identity, money, dates, audit actors and collections.

A value object is defined by its value rather than by an identity: two `OrderId` objects holding
the same UUID are the same order id. This library gives every such type the same three things:
validation at construction, value equality through `sameValueAs()`, and conversion to and from plain
PHP values for persistence and messaging.

## Installation

```bash
composer require dranzd/common-valueobject
```

Requires PHP 8.3 or newer.

## What is included

| Area | Types | Reference |
|---|---|---|
| Contracts | `ValueObject`, `ScalarValueObjectTranslatorCapable`, `ArrayValueObjectTranslatorCapable`, `CompareSameValueTrait` | [core](docs/reference/core.md) |
| Primitives | `Literal` (string), `Integer`, `Real` (float), `Boolean` | [core](docs/reference/core.md) |
| Identity | `Uuid`, `Actor`, `ActorType` | [core](docs/reference/core.md), [actor](docs/reference/actor.md) |
| Money | `Money\Basic`, `Currency`, `CurrencyCode` | [money](docs/reference/money.md) |
| Dates | `Date`, `DateTime`, `Time` | [core](docs/reference/core.md) |
| Collections | `Collection` | [core](docs/reference/core.md) |
| Other composites | `Quantity\Basic`, `File`, `Address` | [composites](docs/reference/composites.md) |
| Search | `Query`, `Page`, `Result`, `Filter`, `Sorter` | [search](docs/reference/search.md) |

All classes live under the `Dranzd\Common\Domain\ValueObject` namespace.

## Usage

### Typed identifiers

Extend `Uuid` to give each kind of id its own type, so an `OrderId` can never be passed where a
`CustomerId` is expected.

```php
use Dranzd\Common\Domain\ValueObject\Identity\Uuid;

final class OrderId extends Uuid
{
}

$id = new OrderId();                               // generates a version 4 UUID
$same = OrderId::fromNative($id->toNative());      // rebuild from storage

$id->sameValueAs($same);                           // true
$id->sameValueAs(Uuid::fromNative($id->toNative())); // false: same value, different type

new OrderId('nope'); // throws InvalidArgumentException: Value "nope" is not a valid UUID.
```

### Your own validated primitives

Extend `Literal`, `Integer`, `Real` or `Boolean` and validate in the constructor.

```php
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;

final class Sku extends Literal
{
    public function __construct(string $value = '')
    {
        if (preg_match('/^[A-Z]{3}-\d{4}$/', $value) !== 1) {
            throw new \InvalidArgumentException(sprintf('Invalid SKU "%s".', $value));
        }

        parent::__construct($value);
    }
}

(new Sku('ABC-1234'))->toNative(); // 'ABC-1234'
new Sku('abc');                    // throws InvalidArgumentException: Invalid SKU "abc".
```

### Money

Amounts are integers in the currency's minor unit (cents), so arithmetic is exact. Operations
return new instances and refuse to mix currencies.

```php
use Dranzd\Common\Domain\ValueObject\Money\Basic as Money;

$price = Money::fromArray(['amount' => 1999, 'currency' => 'USD']);
$shipping = Money::fromArray([500, 'USD']);

$price->add($shipping)->toArray();       // ['amount' => 2499, 'currency' => 'USD']
$price->multiply(3)->toArray();          // ['amount' => 5997, 'currency' => 'USD']
$price->isGreaterThan($shipping);        // true
$price->isLessThanOrEqual($shipping);    // false

$price->add(Money::fromArray([1, 'EUR']));
// throws InvalidArgumentException: Currency mismatch: cannot operate on USD and EUR
```

### Actor

Records who performed an action, for audit trails and event metadata.

```php
use Dranzd\Common\Domain\ValueObject\Identity\Actor;

$actor = Actor::user('7f1d', 'Ada', 'tenant-1');

(string) $actor;      // 'User: Ada (7f1d)'
$actor->toArray();
// ['id' => '7f1d', 'type' => 'User', 'name' => 'Ada', 'tenantId' => 'tenant-1',
//  'branchId' => null, 'sourceSystem' => 'Application']

Actor::fromArray($actor->toArray())->sameValueAs($actor);  // true
(string) Actor::system('Nightly import', 'Scheduler');     // 'System: Nightly import (N/A)'
```

### Dates and collections

```php
use Dranzd\Common\Domain\ValueObject\Collection\Collection;
use Dranzd\Common\Domain\ValueObject\DateTime\Date;
use Dranzd\Common\Domain\ValueObject\Native\Number\Integer;

(new Date('2024-02-29'))->toNative(); // '2024-02-29'
new Date('2024-02-30');
// throws InvalidArgumentException: Expected a date in the format "Y-m-d", got "2024-02-30".

$numbers = Collection::fromArray([new Integer(1), new Integer(2)]);
$numbers->toArray();                           // [1, 2]
$numbers->hasValueObjectItem(new Integer(2));  // true
```

### A composite value object of your own

Implement the contract directly when no base class fits. `CompareSameValueTrait` supplies the
equality check from `toArray()` or `toNative()`.

```php
use Dranzd\Common\Domain\ValueObject\ArrayValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

final class Dimensions implements ValueObject, ArrayValueObjectTranslatorCapable
{
    use CompareSameValueTrait;

    private function __construct(
        private readonly int $widthMm,
        private readonly int $heightMm,
    ) {
        if ($widthMm <= 0 || $heightMm <= 0) {
            throw new \InvalidArgumentException('Dimensions must be positive.');
        }
    }

    public static function fromArray(array $array): self
    {
        return new self((int) $array['width_mm'], (int) $array['height_mm']);
    }

    public function toArray(): array
    {
        return ['width_mm' => $this->widthMm, 'height_mm' => $this->heightMm];
    }

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return sprintf('%d x %d mm', $this->widthMm, $this->heightMm);
    }
}

$a4 = Dimensions::fromArray(['width_mm' => 210, 'height_mm' => 297]);
$a4->sameValueAs(Dimensions::fromArray(['width_mm' => 210, 'height_mm' => 297])); // true
(string) $a4;                                                                     // '210 x 297 mm'
```

## Design notes

- **Immutable.** Value objects do not change after construction; operations return a new instance.
  The exception is `Collection`, which inherits Doctrine's mutators (`add`, `set`, `remove`, `clear`).
- **Equality is by type and value.** `sameValueAs()` is false for different classes even when the
  underlying value matches, which is what keeps typed identifiers apart.
- **Open for extension.** Base classes are deliberately not `final`, and the public and protected
  surface is kept backward compatible across minor releases.
- **Validation at the edge.** Validating constructors and factories throw
  `\InvalidArgumentException`. The reference lists where input is cast rather than validated.

## Development

```bash
composer install
composer check        # coding standards, static analysis and tests
```

| Command | Tool |
|---|---|
| `composer test` | PHPUnit |
| `composer test:coverage` | PHPUnit with coverage (needs pcov or Xdebug) |
| `composer phpstan` | PHPStan, level 8 |
| `composer cs-check` / `composer cs-fix` | PHP_CodeSniffer, PSR-12 |

A Docker setup is included for running the same commands without a local PHP: `make install`, then
`make check`.

CI runs the suite on PHP 8.3, 8.4 and 8.5 and requires at least 95% line coverage.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md). Security issues: see [SECURITY.md](SECURITY.md).

## License

[MIT](LICENSE)
