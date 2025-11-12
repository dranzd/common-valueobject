# 1000 - Test Infrastructure

**Status:** ✅ Completed  
**Priority:** Critical  
**Series:** 1000 - Core Infrastructure & Testing  
**Dependencies:** None

---

## Overview

Establish comprehensive test infrastructure for the `dranzd/common-valueobject` library. This includes setting up PHPUnit configuration, creating base test classes, configuring code coverage reporting, and establishing testing best practices.

## Business Context

A robust test suite is essential for:
- Ensuring value object correctness and reliability
- Preventing regressions during development
- Building confidence in the library for users
- Enabling safe refactoring and enhancements
- Meeting quality standards (80%+ coverage target)

## Objectives

- Set up PHPUnit 11 test infrastructure
- Create base test classes and utilities
- Configure code coverage reporting
- Establish testing patterns and best practices
- Achieve foundation for 80%+ test coverage
- Integrate with CI/CD pipeline

---

## Tasks

### 1. PHPUnit Configuration

- [ ] Review and optimize `phpunit.xml` configuration
  - [ ] Configure test suites (Unit, Integration)
  - [ ] Set up code coverage settings
  - [ ] Configure test directories
  - [ ] Add coverage filters and exclusions
- [ ] Configure coverage reporting formats
  - [ ] HTML coverage reports
  - [ ] Clover XML for CI/CD
  - [ ] Text summary for console
- [ ] Set up test bootstrap file if needed

### 2. Base Test Classes

- [ ] Create `ValueObjectTestCase` base class
  - [ ] Common assertion methods
  - [ ] Value object creation helpers
  - [ ] Equality testing utilities
  - [ ] Serialization test helpers
- [ ] Create test traits for common patterns
  - [ ] `FromNativeTestTrait` - Test fromNative() methods
  - [ ] `ToNativeTestTrait` - Test toNative() methods
  - [ ] `EqualityTestTrait` - Test sameValueAs() methods
  - [ ] `ImmutabilityTestTrait` - Test immutability
  - [ ] `SerializationTestTrait` - Test serialization

### 3. Test Utilities

- [ ] Create test data providers
  - [ ] Valid value generators
  - [ ] Invalid value generators
  - [ ] Edge case generators
  - [ ] Boundary value generators
- [ ] Create assertion helpers
  - [ ] `assertValueObjectEquals()`
  - [ ] `assertValueObjectNotEquals()`
  - [ ] `assertImmutable()`
  - [ ] `assertValidationFails()`
- [ ] Create mock/stub utilities if needed

### 4. Test Organization

- [ ] Create directory structure
  ```
  tests/
  ├── Unit/
  │   ├── ValueObject/
  │   │   ├── Native/
  │   │   ├── Identity/
  │   │   ├── DateTime/
  │   │   ├── Money/
  │   │   ├── Location/
  │   │   ├── File/
  │   │   ├── Collection/
  │   │   └── Search/
  │   └── TestCase.php
  ├── Integration/
  ├── Fixtures/
  └── bootstrap.php
  ```
- [ ] Set up naming conventions
- [ ] Create README for test directory

### 5. Coverage Configuration

- [ ] Configure coverage thresholds
  - [ ] Minimum 80% line coverage
  - [ ] Minimum 80% method coverage
  - [ ] Minimum 70% branch coverage
- [ ] Set up coverage exclusions
  - [ ] Exclude vendor directory
  - [ ] Exclude test files
  - [ ] Exclude generated files
- [ ] Configure coverage reporting
  - [ ] Local HTML reports
  - [ ] CI/CD integration
  - [ ] Coverage badges

### 6. Testing Patterns Documentation

- [ ] Document testing best practices
  - [ ] How to test value objects
  - [ ] Test naming conventions
  - [ ] Test organization guidelines
  - [ ] Coverage requirements
- [ ] Create testing examples
  - [ ] Simple value object test
  - [ ] Complex value object test
  - [ ] Edge case testing
  - [ ] Validation testing
- [ ] Document test data strategies

### 7. Integration with Development Workflow

- [ ] Add composer test scripts
  - [ ] `composer test` - Run all tests
  - [ ] `composer test:unit` - Run unit tests only
  - [ ] `composer test:coverage` - Generate coverage report
  - [ ] `composer test:coverage-html` - Generate HTML coverage
- [ ] Configure test watchers (optional)
- [ ] Set up parallel test execution (optional)

### 8. Quality Gates

- [ ] Define test quality requirements
  - [ ] All tests must pass
  - [ ] Coverage thresholds must be met
  - [ ] No skipped tests in main branch
  - [ ] No incomplete tests in main branch
- [ ] Set up pre-commit hooks (optional)
  - [ ] Run tests before commit
  - [ ] Check coverage before commit
- [ ] Configure CI/CD test gates

---

## Acceptance Criteria

- [ ] PHPUnit 11 is properly configured
- [ ] Base test classes and traits are created and documented
- [ ] Test directory structure is established
- [ ] Code coverage reporting is working (HTML, Clover, Text)
- [ ] Test utilities and helpers are available
- [ ] Testing documentation is complete
- [ ] Composer test scripts are functional
- [ ] Coverage thresholds are configured
- [ ] At least one example test exists and passes
- [ ] Test infrastructure is ready for feature 1001 (Core Value Object Tests)

---

## Example Usage

### Using ValueObjectTestCase

```php
<?php

namespace Dranzd\Common\Tests\Unit\ValueObject\Native\String;

use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Tests\Unit\ValueObject\ValueObjectTestCase;

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
}
```

### Using Test Traits

```php
<?php

namespace Dranzd\Common\Tests\Unit\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\Identity\Uuid;
use Dranzd\Common\Tests\Unit\ValueObject\ValueObjectTestCase;
use Dranzd\Common\Tests\Unit\ValueObject\Traits\FromNativeTestTrait;
use Dranzd\Common\Tests\Unit\ValueObject\Traits\ToNativeTestTrait;
use Dranzd\Common\Tests\Unit\ValueObject\Traits\EqualityTestTrait;

final class UuidTest extends ValueObjectTestCase
{
    use FromNativeTestTrait;
    use ToNativeTestTrait;
    use EqualityTestTrait;
    
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

### Composer Test Scripts

```bash
# Run all tests
composer test

# Run unit tests only
composer test:unit

# Generate coverage report
composer test:coverage

# Generate HTML coverage report
composer test:coverage-html

# Watch tests (if configured)
composer test:watch
```

---

## Technical Design

### Base Test Class Hierarchy

```
TestCase (PHPUnit)
    ↓
ValueObjectTestCase
    ↓
├── NativeTypeTestCase
├── IdentityTestCase
├── DateTimeTestCase
└── ComplexValueObjectTestCase
```

### Test Traits

- **FromNativeTestTrait**: Tests `fromNative()` with valid and invalid values
- **ToNativeTestTrait**: Tests `toNative()` returns correct native type
- **EqualityTestTrait**: Tests `sameValueAs()` for equality and inequality
- **ImmutabilityTestTrait**: Tests that value objects cannot be modified
- **SerializationTestTrait**: Tests JSON/array serialization

### Coverage Strategy

1. **Line Coverage**: Ensure every line of code is executed
2. **Branch Coverage**: Test all conditional branches
3. **Method Coverage**: Test all public methods
4. **Edge Cases**: Test boundary conditions and edge cases
5. **Error Cases**: Test validation and error handling

---

## Related Features

- **1001 - Core Value Object Tests**: Uses this infrastructure
- **1002 - Advanced Value Object Tests**: Uses this infrastructure
- **1004 - CI/CD Pipeline**: Integrates with this infrastructure

---

**Created:** November 9, 2025  
**Target Completion:** November 16, 2025  
**Estimated Effort:** 2-3 days

---

## Implementation Addendum

**Date:** November 13, 2025  
**Status:** ✅ Completed

### What Was Implemented

1. **PHPUnit 11 Configuration**
   - Comprehensive `phpunit.xml` with test suites (Unit, Integration)
   - Code coverage configuration (HTML, Clover, Text)
   - Strict testing settings enabled
   - Source filtering and exclusions configured

2. **Base Test Classes**
   - `ValueObjectTestCase` - Base class with common assertion methods
   - Custom assertions: `assertValueObjectEquals`, `assertValueObjectNotEquals`, `assertImmutable`, `assertSerializable`, `assertJsonSerializable`
   - Helper methods for value object testing

3. **Test Traits**
   - `FromNativeTestTrait` - Tests `fromNative()` with valid/invalid values
   - `ToNativeTestTrait` - Tests `toNative()` returns correct native type
   - `EqualityTestTrait` - Tests equality properties (reflexive, symmetric, transitive)
   - `SerializationTestTrait` - Tests serialization and JSON encoding

4. **Directory Structure**
   - `tests/Unit/` - Unit tests directory
   - `tests/Integration/` - Integration tests directory
   - `tests/Fixtures/` - Test fixtures directory
   - `tests/Unit/Traits/` - Reusable test traits
   - `tests/Unit/ValueObject/Identity/` - Identity value object tests

5. **Composer Scripts**
   - `composer test` - Run all tests
   - `composer test:unit` - Run unit tests only
   - `composer test:integration` - Run integration tests only
   - `composer test:coverage` - Generate text coverage report
   - `composer test:coverage-html` - Generate HTML coverage report
   - `composer test:coverage-clover` - Generate Clover XML report
   - `composer quality` - Run all quality checks

6. **Documentation**
   - Comprehensive `tests/README.md` with usage examples
   - Testing best practices and guidelines
   - Coverage requirements documented
   - Example tests provided

7. **Example Test**
   - `UuidTest.php` - Example test using all test traits
   - Demonstrates proper test structure and usage

### Files Created

**Configuration:**
- `phpunit.xml` (updated)
- `.gitignore` (updated - added coverage directories)

**Test Infrastructure:**
- `tests/Unit/ValueObjectTestCase.php`
- `tests/Unit/Traits/FromNativeTestTrait.php`
- `tests/Unit/Traits/ToNativeTestTrait.php`
- `tests/Unit/Traits/EqualityTestTrait.php`
- `tests/Unit/Traits/SerializationTestTrait.php`

**Documentation:**
- `tests/README.md`

**Example Tests:**
- `tests/Unit/ValueObject/Identity/UuidTest.php`

**Directories:**
- `tests/Integration/`
- `tests/Fixtures/`

### Files Modified

- `composer.json` - Added test scripts
- `.gitignore` - Added coverage and cache directories

### Design Decisions

1. **PHPUnit 11 Strict Mode**: Enabled strict settings for better test quality
2. **Trait-Based Testing**: Reusable traits reduce boilerplate and ensure consistency
3. **Comprehensive Assertions**: Custom assertions make tests more readable
4. **Separate Test Suites**: Unit and Integration tests can be run independently
5. **Multiple Coverage Formats**: HTML for local, Clover for CI/CD, Text for console
6. **Quality Script**: Combined test + phpstan + code style for one-command verification

### Testing Strategy

- **Base Test Case**: Provides common functionality for all value object tests
- **Test Traits**: Reduce code duplication and ensure consistent testing patterns
- **Data Providers**: Used in traits for testing multiple values
- **Immutability Checks**: Reflection-based verification of no setters or mutable properties
- **Serialization Tests**: Ensure value objects can be used in events and APIs

### Next Steps

1. Implement Feature 1001 (Core Value Object Tests) using this infrastructure
2. Add more example tests as value objects are tested
3. Consider adding performance benchmarking tests
4. Integrate with CI/CD pipeline (Feature 1004)
5. Add mutation testing for higher quality assurance

### Notes

- Test infrastructure is ready for immediate use
- All test traits follow PHPUnit 11 best practices
- Coverage thresholds can be adjusted in phpunit.xml
- Example test demonstrates proper usage of all components
