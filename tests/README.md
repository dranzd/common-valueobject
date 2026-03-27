# Test Suite

This directory contains the test suite for the `dranzd/common-valueobject` library.

## Structure

```
tests/
├── Unit/                           # Unit tests
│   ├── ValueObjectTestCase.php    # Base test case for value objects
│   ├── Traits/                     # Reusable test traits
│   │   ├── FromNativeTestTrait.php
│   │   ├── ToNativeTestTrait.php
│   │   ├── EqualityTestTrait.php
│   │   └── SerializationTestTrait.php
│   └── ValueObject/                # Value object tests
│       ├── Native/                 # Native type tests
│       ├── Identity/               # Identity tests (UUID, Actor)
│       ├── DateTime/               # DateTime tests
│       ├── Money/                  # Money tests (Basic, Currency, CurrencyCode)
│       ├── Location/               # Location tests
│       ├── Collection/             # Collection tests
│       └── Search/                 # Search component tests
├── Integration/                    # Integration tests
├── Fixtures/                       # Test fixtures and data
└── README.md                       # This file
```

## Running Tests

### Using Composer Scripts

```bash
# Run all tests
composer test

# Run unit tests only
composer test:unit

# Run integration tests only
composer test:integration

# Run with coverage report (text)
composer test:coverage

# Generate HTML coverage report
composer test:coverage-html

# Generate Clover XML coverage report
composer test:coverage-clover

# Run all quality checks (tests + phpstan + code style)
composer quality
```

### Using Utils Script

```bash
# Run all tests
./utils test

# Run with coverage
./utils test:coverage

# Run all quality checks
./utils quality
```

### Using PHPUnit Directly

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test suite
vendor/bin/phpunit --testsuite=Unit

# Run specific test file
vendor/bin/phpunit tests/Unit/ValueObject/Identity/UuidTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html=coverage/html
```

## Writing Tests

### Basic Value Object Test

```php
<?php

namespace Dranzd\Common\Domain\ValueObject\Tests\Unit\ValueObject\Native\String;

use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\ValueObjectTestCase;

final class LiteralTest extends ValueObjectTestCase
{
    public function testFromNative(): void
    {
        $value = 'Hello World';
        $literal = Literal::fromNative($value);

        $this->assertInstanceOf(Literal::class, $literal);
        $this->assertEquals($value, $literal->toNative());
    }

    public function testSameValueAs(): void
    {
        $literal1 = Literal::fromNative('test');
        $literal2 = Literal::fromNative('test');
        $literal3 = Literal::fromNative('different');

        $this->assertValueObjectEquals($literal1, $literal2);
        $this->assertValueObjectNotEquals($literal1, $literal3);
    }

    public function testImmutability(): void
    {
        $literal = Literal::fromNative('test');
        $this->assertImmutable($literal);
    }

    public function testSerialization(): void
    {
        $literal = Literal::fromNative('test');
        $this->assertSerializable($literal);
        $this->assertJsonSerializable($literal);
    }
}
```

### Using Test Traits

```php
<?php

namespace Dranzd\Common\Domain\ValueObject\Tests\Unit\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\Identity\Uuid;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\ValueObjectTestCase;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits\FromNativeTestTrait;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits\ToNativeTestTrait;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits\EqualityTestTrait;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits\SerializationTestTrait;

final class UuidTest extends ValueObjectTestCase
{
    use FromNativeTestTrait;
    use ToNativeTestTrait;
    use EqualityTestTrait;
    use SerializationTestTrait;

    protected function getValueObjectClass(): string
    {
        return Uuid::class;
    }

    protected function getValidNativeValues(): array
    {
        return [
            '550e8400-e29b-41d4-a716-446655440000',
            '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
            '6ba7b811-9dad-11d1-80b4-00c04fd430c8',
        ];
    }

    protected function getInvalidNativeValues(): array
    {
        return [
            'not-a-uuid',
            '123',
            '',
            'invalid-uuid-format',
        ];
    }
}
```

## Test Utilities

### ValueObjectTestCase

Base test case providing common assertion methods:

- `assertValueObjectEquals($expected, $actual)` - Assert two value objects are equal
- `assertValueObjectNotEquals($expected, $actual)` - Assert two value objects are not equal
- `assertImmutable($valueObject)` - Assert value object has no setters
- `assertValidationFails($exception, $callback)` - Assert validation throws exception
- `assertSerializable($valueObject)` - Assert value object can be serialized/deserialized
- `assertJsonSerializable($valueObject)` - Assert value object can be JSON encoded/decoded

### Test Traits

- **FromNativeTestTrait** - Tests `fromNative()` with valid and invalid values
- **ToNativeTestTrait** - Tests `toNative()` returns correct native type
- **EqualityTestTrait** - Tests `sameValueAs()` for equality properties (reflexive, symmetric, transitive)
- **SerializationTestTrait** - Tests serialization and JSON encoding

## Testing Best Practices

### 1. Test Organization

- One test class per value object class
- Group related tests using methods or data providers
- Use descriptive test method names

### 2. Test Coverage

- Aim for 80%+ code coverage
- Test happy paths and edge cases
- Test validation and error handling
- Test immutability

### 3. Test Data

- Use realistic test data
- Test boundary values
- Test edge cases (empty, null, very large, special characters)
- Use data providers for multiple test cases

### 4. Assertions

- Use specific assertions (`assertEquals`, `assertInstanceOf`, etc.)
- Use custom assertions from `ValueObjectTestCase`
- Add meaningful assertion messages

### 5. Test Independence

- Each test should be independent
- Don't rely on test execution order
- Clean up after tests if needed

## Code Coverage

### Viewing Coverage Reports

After running tests with coverage:

```bash
# Generate HTML coverage
composer test:coverage-html

# Open in browser
open coverage/html/index.html
```

### Coverage Requirements

- **Minimum Line Coverage**: 80%
- **Minimum Method Coverage**: 80%
- **Minimum Branch Coverage**: 70%

### Excluded from Coverage

- Exception classes
- Generated code
- Vendor directory

## Continuous Integration

Tests are automatically run on:
- Every push to any branch
- Every pull request
- Before merging to main

CI checks:
- ✅ All tests pass
- ✅ Code coverage meets thresholds
- ✅ PHPStan level 8+ passes
- ✅ Code style (PSR-12) passes

## Troubleshooting

### Tests Not Found

Ensure autoload is updated:
```bash
composer dump-autoload
```

### Coverage Not Working

Ensure Xdebug or PCOV is installed:
```bash
php -m | grep -i xdebug
```

### Slow Tests

Use parallel execution (if configured):
```bash
vendor/bin/phpunit --parallel
```

## Contributing

When adding new value objects:

1. Create corresponding test class
2. Use test traits where applicable
3. Achieve 80%+ coverage
4. Ensure all tests pass
5. Update this README if needed

---

**Last Updated:** November 13, 2025
