# 1001 - Core Value Object Tests

**Status:** 🔵 Planned  
**Priority:** Critical  
**Series:** 1000 - Core Infrastructure & Testing  
**Dependencies:** 1000 (Test Infrastructure)

---

## Overview

Implement comprehensive unit tests for all core value objects in the library, including native types, identity, datetime, money, and quantity value objects. This ensures reliability and correctness of the fundamental building blocks.

## Business Context

Core value objects are the foundation of the library. They must be:
- Thoroughly tested for correctness
- Validated for edge cases and boundary conditions
- Proven to be immutable and self-validating
- Verified for proper equality comparison
- Tested for serialization/deserialization

High test coverage (≥80%) provides confidence to users and enables safe refactoring.

## Objectives

- Achieve 80%+ test coverage for core value objects
- Test all public methods and behaviors
- Validate edge cases and error conditions
- Verify immutability and equality semantics
- Document testing patterns for future value objects
- Establish quality baseline for the library

---

## Tasks

### 1. Native Type Tests

#### Boolean Value Object
- [ ] Test `fromNative()` with true/false
- [ ] Test `toNative()` returns correct boolean
- [ ] Test `sameValueAs()` equality
- [ ] Test string representation
- [ ] Test edge cases (null, non-boolean)
- [ ] Test immutability

#### Integer Value Object
- [ ] Test `fromNative()` with various integers
- [ ] Test `toNative()` returns correct integer
- [ ] Test `sameValueAs()` equality
- [ ] Test positive, negative, zero values
- [ ] Test boundary values (PHP_INT_MAX, PHP_INT_MIN)
- [ ] Test invalid inputs (float, string, null)
- [ ] Test immutability

#### Real (Float) Value Object
- [ ] Test `fromNative()` with various floats
- [ ] Test `toNative()` returns correct float
- [ ] Test `sameValueAs()` with float precision
- [ ] Test positive, negative, zero values
- [ ] Test scientific notation
- [ ] Test special values (INF, -INF, NAN)
- [ ] Test invalid inputs
- [ ] Test immutability

#### Literal (String) Value Object
- [ ] Test `fromNative()` with various strings
- [ ] Test `toNative()` returns correct string
- [ ] Test `sameValueAs()` equality
- [ ] Test empty string
- [ ] Test special characters
- [ ] Test Unicode strings
- [ ] Test very long strings
- [ ] Test invalid inputs (null, array)
- [ ] Test immutability

### 2. Identity Tests

#### Uuid Value Object
- [ ] Test `fromNative()` with valid UUIDs
- [ ] Test `generate()` creates valid UUID
- [ ] Test `toNative()` returns UUID string
- [ ] Test `sameValueAs()` equality
- [ ] Test UUID v4 format validation
- [ ] Test invalid UUID formats
- [ ] Test case sensitivity
- [ ] Test with/without hyphens
- [ ] Test immutability
- [ ] Test uniqueness of generated UUIDs

### 3. DateTime Tests

#### Date Value Object
- [ ] Test `fromNative()` with valid dates
- [ ] Test `toNative()` returns date string
- [ ] Test `sameValueAs()` equality
- [ ] Test various date formats
- [ ] Test edge dates (leap years, month boundaries)
- [ ] Test invalid dates
- [ ] Test date comparison
- [ ] Test immutability
- [ ] Test timezone handling (if applicable)

#### DateTime Value Object
- [ ] Test `fromNative()` with valid datetimes
- [ ] Test `toNative()` returns datetime string
- [ ] Test `sameValueAs()` equality
- [ ] Test various datetime formats
- [ ] Test with/without timezone
- [ ] Test edge datetimes
- [ ] Test invalid datetimes
- [ ] Test datetime comparison
- [ ] Test immutability
- [ ] Test timezone conversion (if applicable)

#### Time Value Object
- [ ] Test `fromNative()` with valid times
- [ ] Test `toNative()` returns time string
- [ ] Test `sameValueAs()` equality
- [ ] Test various time formats (12h, 24h)
- [ ] Test edge times (00:00:00, 23:59:59)
- [ ] Test invalid times
- [ ] Test time comparison
- [ ] Test immutability

### 4. Money Tests

#### Money (Basic) Value Object
- [ ] Test `fromNative()` with amount and currency
- [ ] Test `toNative()` returns array
- [ ] Test `sameValueAs()` equality
- [ ] Test `getAmount()` returns correct amount
- [ ] Test `getCurrency()` returns correct currency
- [ ] Test various currencies (USD, EUR, GBP, etc.)
- [ ] Test zero amount
- [ ] Test negative amounts
- [ ] Test large amounts
- [ ] Test decimal precision
- [ ] Test invalid currency codes
- [ ] Test invalid amounts
- [ ] Test immutability

### 5. Quantity Tests

#### Quantity (Basic) Value Object
- [ ] Test `fromNative()` with value and unit
- [ ] Test `toNative()` returns array
- [ ] Test `sameValueAs()` equality
- [ ] Test `getValue()` returns correct value
- [ ] Test `getUnit()` returns correct unit
- [ ] Test various units (kg, m, l, etc.)
- [ ] Test zero quantity
- [ ] Test negative quantities
- [ ] Test decimal quantities
- [ ] Test invalid units
- [ ] Test invalid values
- [ ] Test immutability

### 6. Cross-Cutting Tests

- [ ] Test all value objects implement `ValueObject` interface
- [ ] Test all value objects use `CompareSameValueTrait` correctly
- [ ] Test all value objects are properly immutable
- [ ] Test all value objects handle null inputs correctly
- [ ] Test all value objects throw appropriate exceptions
- [ ] Test serialization/deserialization for all types
- [ ] Test string representation for all types

### 7. Documentation

- [ ] Add PHPDoc to all test methods
- [ ] Document test data providers
- [ ] Create testing guide for core value objects
- [ ] Add examples of common test patterns
- [ ] Document edge cases and why they're tested

---

## Acceptance Criteria

- [ ] All core value objects have comprehensive unit tests
- [ ] Test coverage ≥80% for all core value objects
- [ ] All public methods are tested
- [ ] Edge cases and boundary conditions are tested
- [ ] Error conditions and validation are tested
- [ ] Immutability is verified for all value objects
- [ ] Equality comparison is tested for all value objects
- [ ] All tests pass with PHPUnit 11
- [ ] Test code follows PSR-12 standards
- [ ] Tests are well-documented with clear intent

---

## Example Tests

### Boolean Test Example

```php
<?php

namespace Dranzd\Common\Tests\Unit\ValueObject\Native\Logic;

use Dranzd\Common\Domain\ValueObject\Native\Logic\Boolean;
use Dranzd\Common\Tests\Unit\ValueObject\ValueObjectTestCase;

final class BooleanTest extends ValueObjectTestCase
{
    public function testFromNativeWithTrue(): void
    {
        $boolean = Boolean::fromNative(true);
        
        $this->assertInstanceOf(Boolean::class, $boolean);
        $this->assertTrue($boolean->toNative());
    }
    
    public function testFromNativeWithFalse(): void
    {
        $boolean = Boolean::fromNative(false);
        
        $this->assertInstanceOf(Boolean::class, $boolean);
        $this->assertFalse($boolean->toNative());
    }
    
    public function testSameValueAs(): void
    {
        $true1 = Boolean::fromNative(true);
        $true2 = Boolean::fromNative(true);
        $false1 = Boolean::fromNative(false);
        
        $this->assertTrue($true1->sameValueAs($true2));
        $this->assertFalse($true1->sameValueAs($false1));
    }
    
    public function testToString(): void
    {
        $true = Boolean::fromNative(true);
        $false = Boolean::fromNative(false);
        
        $this->assertEquals('1', (string) $true);
        $this->assertEquals('0', (string) $false);
    }
    
    public function testImmutability(): void
    {
        $boolean = Boolean::fromNative(true);
        
        // Verify no setter methods exist
        $this->assertImmutable($boolean);
    }
}
```

### UUID Test Example

```php
<?php

namespace Dranzd\Common\Tests\Unit\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\Identity\Uuid;
use Dranzd\Common\Tests\Unit\ValueObject\ValueObjectTestCase;

final class UuidTest extends ValueObjectTestCase
{
    public function testFromNativeWithValidUuid(): void
    {
        $uuidString = '550e8400-e29b-41d4-a716-446655440000';
        $uuid = Uuid::fromNative($uuidString);
        
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertEquals($uuidString, $uuid->toNative());
    }
    
    public function testGenerate(): void
    {
        $uuid = Uuid::generate();
        
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid->toNative()
        );
    }
    
    public function testSameValueAs(): void
    {
        $uuid1 = Uuid::fromNative('550e8400-e29b-41d4-a716-446655440000');
        $uuid2 = Uuid::fromNative('550e8400-e29b-41d4-a716-446655440000');
        $uuid3 = Uuid::fromNative('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        
        $this->assertTrue($uuid1->sameValueAs($uuid2));
        $this->assertFalse($uuid1->sameValueAs($uuid3));
    }
    
    /**
     * @dataProvider invalidUuidProvider
     */
    public function testFromNativeWithInvalidUuid(mixed $invalidUuid): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        Uuid::fromNative($invalidUuid);
    }
    
    public static function invalidUuidProvider(): array
    {
        return [
            'not a uuid' => ['not-a-uuid'],
            'empty string' => [''],
            'numeric' => [123],
            'invalid format' => ['550e8400-e29b-41d4-a716'],
        ];
    }
    
    public function testUniqueness(): void
    {
        $uuid1 = Uuid::generate();
        $uuid2 = Uuid::generate();
        
        $this->assertFalse($uuid1->sameValueAs($uuid2));
    }
}
```

### Money Test Example

```php
<?php

namespace Dranzd\Common\Tests\Unit\ValueObject\Money;

use Dranzd\Common\Domain\ValueObject\Money\Basic as Money;
use Dranzd\Common\Tests\Unit\ValueObject\ValueObjectTestCase;

final class BasicTest extends ValueObjectTestCase
{
    public function testFromNative(): void
    {
        $money = Money::fromNative(['amount' => 1999, 'currency' => 'USD']);
        
        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(1999, $money->getAmount());
        $this->assertEquals('USD', $money->getCurrency());
    }
    
    public function testToNative(): void
    {
        $money = Money::fromNative(['amount' => 1999, 'currency' => 'USD']);
        
        $this->assertEquals(
            ['amount' => 1999, 'currency' => 'USD'],
            $money->toNative()
        );
    }
    
    public function testSameValueAs(): void
    {
        $money1 = Money::fromNative(['amount' => 1999, 'currency' => 'USD']);
        $money2 = Money::fromNative(['amount' => 1999, 'currency' => 'USD']);
        $money3 = Money::fromNative(['amount' => 2999, 'currency' => 'USD']);
        $money4 = Money::fromNative(['amount' => 1999, 'currency' => 'EUR']);
        
        $this->assertTrue($money1->sameValueAs($money2));
        $this->assertFalse($money1->sameValueAs($money3));
        $this->assertFalse($money1->sameValueAs($money4));
    }
    
    /**
     * @dataProvider currencyProvider
     */
    public function testVariousCurrencies(string $currency): void
    {
        $money = Money::fromNative(['amount' => 1000, 'currency' => $currency]);
        
        $this->assertEquals($currency, $money->getCurrency());
    }
    
    public static function currencyProvider(): array
    {
        return [
            'USD' => ['USD'],
            'EUR' => ['EUR'],
            'GBP' => ['GBP'],
            'JPY' => ['JPY'],
            'CHF' => ['CHF'],
        ];
    }
    
    public function testZeroAmount(): void
    {
        $money = Money::fromNative(['amount' => 0, 'currency' => 'USD']);
        
        $this->assertEquals(0, $money->getAmount());
    }
    
    public function testNegativeAmount(): void
    {
        $money = Money::fromNative(['amount' => -1000, 'currency' => 'USD']);
        
        $this->assertEquals(-1000, $money->getAmount());
    }
}
```

---

## Test Coverage Goals

| Component | Target Coverage | Priority |
|-----------|----------------|----------|
| Native/Logic/Boolean | 100% | Critical |
| Native/Number/Integer | 100% | Critical |
| Native/Number/Real | 100% | Critical |
| Native/String/Literal | 100% | Critical |
| Identity/Uuid | 95%+ | Critical |
| DateTime/Date | 90%+ | Critical |
| DateTime/DateTime | 90%+ | Critical |
| DateTime/Time | 90%+ | Critical |
| Money/Basic | 95%+ | Critical |
| Quantity/Basic | 95%+ | High |

---

## Related Features

- **1000 - Test Infrastructure**: Provides base classes and utilities
- **1002 - Advanced Value Object Tests**: Tests remaining value objects
- **1003 - PHPStan Cleanup**: Static analysis improvements

---

**Created:** November 9, 2025  
**Target Completion:** November 20, 2025  
**Estimated Effort:** 3-4 days
