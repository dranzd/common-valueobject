# Common ValueObject

A framework-agnostic Domain-Driven Design (DDD) value object library providing common implementations and utilities for building domain models with immutable, self-validating value objects.

## Features

- **DDD Value Objects**: Immutable, self-validating domain primitives
- **Rich Built-in Types**: Native types, DateTime, Money, UUID, Location, and more
- **Type Safety**: Full PHP 8.3+ type hints with strict typing
- **Framework Agnostic**: No framework dependencies, pure domain logic
- **Validation**: Domain assertion utilities via `dranzd/common-domain-assert`
- **Collection Support**: Type-safe collection implementations
- **Search Components**: Criteria, filters, and pagination value objects
- **Extensible**: Easy to create custom value objects

## Requirements

- PHP 8.3 or higher
- ext-json
- ramsey/uuid ^4.7
- dranzd/common-domain-assert ^1.0 (private library)

## Installation

```bash
composer require dranzd/common-valueobject
```

The required dependencies will be installed automatically.

## Quick Start

### 1. Using Built-in Value Objects

```php
use Dranzd\Common\Domain\ValueObject\Identity\Uuid;
use Dranzd\Common\Domain\ValueObject\Identity\Actor;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Native\Number\Integer;

// UUID value object
$userId = Uuid::fromNative('550e8400-e29b-41d4-a716-446655440000');
echo $userId->toNative(); // "550e8400-e29b-41d4-a716-446655440000"

// Actor value object (for audit trails and event sourcing)
$actor = Actor::user(
    id: 'usr-123',
    name: 'John Doe',
    tenantId: 'tenant-1',
    branchId: 'branch-5'
);
echo $actor->name(); // "John Doe"

// String literal
$name = Literal::fromNative('John Doe');
echo $name->toNative(); // "John Doe"

// Integer
$age = Integer::fromNative(30);
echo $age->toNative(); // 30
```

### 2. Creating Custom Value Objects

```php
use Dranzd\Common\ValueObject\ValueObject;
use Dranzd\Common\ValueObject\CompareSameValueTrait;

final class EmailAddress implements ValueObject
{
    use CompareSameValueTrait;

    private string $value;

    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public static function fromNative($value): self
    {
        return new self($value);
    }

    public function toNative(): string
    {
        return $this->value;
    }

    private function validate(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

// Usage
$email = EmailAddress::fromNative('user@example.com');
echo $email->toNative(); // "user@example.com"
```

### 3. Working with Money

```php
use Dranzd\Common\Domain\ValueObject\Money\Basic as Money;
use Dranzd\Common\Domain\ValueObject\Money\Currency;
use Dranzd\Common\Domain\ValueObject\Money\CurrencyCode;

// Create from array
$price = Money::fromArray(['amount' => 1999, 'currency' => 'USD']);
echo $price->getAmount()->toNative(); // 1999
echo $price->getCurrency()->toNative(); // "USD"

// Currency via static factory (validated against CurrencyCode enum)
$usd = Currency::USD();
$eur = Currency::EUR();
$php = Currency::PHP();

// Or from the enum directly
$currency = Currency::fromCode(CurrencyCode::SGD);

// Arithmetic (returns new immutable instance)
$a = Money::fromArray(['amount' => 5000, 'currency' => 'USD']);
$b = Money::fromArray(['amount' => 3000, 'currency' => 'USD']);
$sum = $a->add($b);          // 8000 USD
$diff = $a->subtract($b);    // 2000 USD
$triple = $a->multiply(3);   // 15000 USD

// Comparisons (enforce same currency)
$a->isGreaterThan($b);       // true
$a->isGreaterThanOrEqual($b); // true
$b->isLessThan($a);          // true
$a->equals($b);              // false

// State checks
$a->isZero();     // false
$a->isPositive(); // true
$a->isNegative(); // false
```

### 4. DateTime Value Objects

```php
use Dranzd\Common\ValueObject\DateTime\DateTime;
use Dranzd\Common\ValueObject\DateTime\Date;

$createdAt = DateTime::fromNative('2025-11-02 08:00:00');
$birthDate = Date::fromNative('1990-01-15');
```

## Built-in Value Objects

### Native Types
- **Boolean** - Boolean value object
- **Integer** - Integer value object
- **Real** - Float/decimal value object
- **Literal** - String value object

### Identity
- **Uuid** - UUID value object (uses ramsey/uuid)

### DateTime
- **DateTime** - Date and time value object
- **Date** - Date-only value object
- **Time** - Time-only value object

### Money
- **Basic** - Money value object with amount, currency, arithmetic, comparisons, and state checks
- **Currency** - Currency value object with static factories (e.g. `Currency::USD()`)
- **CurrencyCode** - PHP string-backed enum of ISO 4217 currency codes (extendable)

### Quantity
- **Basic** - Quantity value object with value and unit

### Location
- **Address** - Address value object

### File
- **File** - File value object

### Collection
- **Collection** - Generic collection value object

### Search Components
- **Criteria** - Search criteria value object
- **Filter** - Filter value object
- **Page** - Pagination value object
- **Query** - Search query value object
- **Result** - Search result value object
- **Sorter** - Sorting value object

## Architecture

This library follows **Domain-Driven Design** principles:

```
┌─────────────────────────────────────┐
│   Infrastructure Layer              │
│   (Framework Adapters)              │
├─────────────────────────────────────┤
│   Application Layer                 │
│   (Use Cases, Services)             │
├─────────────────────────────────────┤
│   Domain Layer                      │
│   (Entities, Value Objects)         │
│   ← This Library                    │
└─────────────────────────────────────┘
```

Value objects are immutable and self-validating, ensuring domain integrity at the core.

## Development

### Running Tests

```bash
composer test
```

### Static Analysis

```bash
composer phpstan
```

### Code Style

```bash
# Check code style
composer cs-check

# Fix code style automatically
composer cs-fix
```

### Using Docker

This library includes a `utils` script (similar to Laravel Sail) for easy Docker management:

```bash
# Start containers
./utils up

# Stop containers
./utils down

# Rebuild containers
./utils rebuild

# Open shell
./utils shell

# Run composer commands
./utils install
./utils update
./utils composer require package/name

# Run tests
./utils test

# Run static analysis
./utils phpstan

# Check/fix code style
./utils cs-check
./utils cs-fix

# Run all quality checks
./utils quality

# Show all available commands
./utils
```

**Traditional Docker Compose:**
```bash
# Build the container
docker-compose build

# Run tests in container
docker-compose exec php composer test

# Interactive shell
docker-compose exec php bash
```

## Design Principles

- **Immutability**: Value objects are immutable once created
- **Self-Validation**: Validation happens in the constructor
- **Type Safety**: Strong typing with PHP 8.3+ features
- **Equality by Value**: Two value objects are equal if their values are equal
- **Framework Agnostic**: No framework dependencies
- **Testability**: Easy to unit test

## Value Object Pattern

All value objects in this library follow these principles:

1. **Immutable**: Once created, cannot be changed
2. **Self-Validating**: Invalid states are impossible
3. **Comparable**: Implements `sameValueAs()` for equality comparison
4. **Serializable**: Can be converted to/from native types
5. **Expressive**: Named constructors for clarity

## Documentation

### Guides
- **[Creating Custom Value Objects](docs/guides/creating-value-objects.md)** - Complete guide to building your own value objects
- **[Built-in Value Objects](docs/guides/built-in-value-objects.md)** - Reference for all included value objects

### Examples
- **[Custom Value Object Examples](docs/examples/custom-value-object-example.md)** - Real-world examples (Email, Phone, Price, DateRange, Percentage)

### Migration
- **[Strip-Down Complete](docs/migration/stripdown-complete.md)** - Summary of library transformation
- **[ValueObject Strip-Down Plan](docs/migration/valueobject-stripdown-plan.md)** - Detailed execution plan

## License

MIT

## Contributing

Contributions are welcome! Please ensure:
- All tests pass
- Code follows PSR-12 standards
- PHPStan analysis passes at level 8 or higher
- New features include tests and documentation

## Changelog

### v1.0.0 (Initial Release)
- ✅ Complete value object library extracted from event-sourcing
- ✅ PHP 8.3 support
- ✅ Integration with `dranzd/common-domain-assert`
- ✅ 30+ built-in value object implementations
- ✅ Comprehensive utilities for common use cases
