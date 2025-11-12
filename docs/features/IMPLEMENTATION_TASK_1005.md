# Implementation Task: Feature 1005 - Actor Value Object

**Feature:** [1005 - Actor Value Object](1005-actor-value-object.md)  
**Status:** Ready for Implementation  
**Priority:** High  
**Estimated Effort:** 2-3 days

---

## Quick Summary

Implement the **Actor Value Object** - a shared kernel component that represents who or what initiated a domain action. This value object replaces simple string references with a structured, strongly-typed identity object supporting User, System, Integration, and Unknown actor types.

---

## Implementation Checklist

### Phase 1: Core Implementation (Day 1)

#### 1.1 Create ActorType Enum
- [ ] Create file: `src/Domain/ValueObject/Identity/ActorType.php`
- [ ] Define enum with cases: User, System, Integration, Unknown
- [ ] Use backed enum with string values
- [ ] Add PHPDoc comments

**File:** `src/Domain/ValueObject/Identity/ActorType.php`

```php
<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Identity;

/**
 * Defines the type of actor that initiated a domain action.
 */
enum ActorType: string
{
    case User = 'User';
    case System = 'System';
    case Integration = 'Integration';
    case Unknown = 'Unknown';
}
```

#### 1.2 Create Actor Value Object
- [ ] Create file: `src/Domain/ValueObject/Identity/Actor.php`
- [ ] Implement `ValueObject` interface
- [ ] Add `CompareSameValueTrait` for equality
- [ ] Define private constructor with all properties
- [ ] Implement `fromNative()` method
- [ ] Implement `toNative()` method
- [ ] Add getter methods for all properties
- [ ] Implement `__toString()` method
- [ ] Add comprehensive PHPDoc comments

**File:** `src/Domain/ValueObject/Identity/Actor.php`

#### 1.3 Implement Factory Methods
- [ ] Implement `Actor::user()` static method
- [ ] Implement `Actor::system()` static method
- [ ] Implement `Actor::integration()` static method
- [ ] Implement `Actor::unknown()` static method
- [ ] Ensure proper parameter validation
- [ ] Add PHPDoc for each factory method

### Phase 2: Testing (Day 2)

#### 2.1 Create Test File
- [ ] Create file: `tests/Unit/ValueObject/Identity/ActorTest.php`
- [ ] Extend appropriate base test class
- [ ] Set up test fixtures

**File:** `tests/Unit/ValueObject/Identity/ActorTest.php`

#### 2.2 Test Factory Methods
- [ ] Test `Actor::user()` creates correct instance
- [ ] Test `Actor::system()` creates correct instance
- [ ] Test `Actor::integration()` creates correct instance
- [ ] Test `Actor::unknown()` creates correct instance
- [ ] Test factory methods with various parameter combinations
- [ ] Test optional parameters have correct defaults

#### 2.3 Test Serialization
- [ ] Test `toNative()` returns correct array structure
- [ ] Test `fromNative()` reconstructs actor correctly
- [ ] Test round-trip serialization (toNative -> fromNative)
- [ ] Test `fromNative()` with invalid data throws exception
- [ ] Test `fromNative()` with missing optional fields

#### 2.4 Test Equality
- [ ] Test two identical actors are equal
- [ ] Test actors with different properties are not equal
- [ ] Test equality with same values but different instances

#### 2.5 Test String Representation
- [ ] Test `__toString()` returns expected format
- [ ] Test `__toString()` with null values

#### 2.6 Test Getters
- [ ] Test all getter methods return correct values
- [ ] Test getters with null values

#### 2.7 Test Edge Cases
- [ ] Test with empty strings (should they be allowed?)
- [ ] Test with very long strings
- [ ] Test with special characters in names
- [ ] Test immutability (no way to modify after construction)

### Phase 3: Quality Assurance (Day 2-3)

#### 3.1 Code Quality
- [ ] Run PHPStan: `./utils phpstan`
- [ ] Fix any PHPStan errors or warnings
- [ ] Run code style check: `./utils cs-check`
- [ ] Fix any code style issues: `./utils cs-fix`
- [ ] Ensure all code follows PSR-12

#### 3.2 Test Coverage
- [ ] Run tests: `./utils test`
- [ ] Generate coverage report: `./utils test:coverage`
- [ ] Verify 80%+ code coverage
- [ ] Add tests for any uncovered lines

#### 3.3 Integration Verification
- [ ] Verify Actor works with existing value object infrastructure
- [ ] Test that CompareSameValueTrait works correctly
- [ ] Verify serialization works with JSON encoding
- [ ] Test in a sample domain event scenario

### Phase 4: Documentation (Day 3)

#### 4.1 Code Documentation
- [ ] Verify all public methods have PHPDoc comments
- [ ] Add `@throws` tags for exceptions
- [ ] Add usage examples in class-level PHPDoc
- [ ] Document all parameters and return types

#### 4.2 Usage Examples
- [ ] Create example file: `docs/examples/actor-value-object.md`
- [ ] Add basic usage examples
- [ ] Add domain event integration examples
- [ ] Add serialization examples

#### 4.3 Update Main Documentation
- [ ] Add Actor to README.md value object list
- [ ] Add Actor usage example to README.md
- [ ] Update value object catalog (if exists)

---

## Files to Create

1. `src/Domain/ValueObject/Identity/ActorType.php` - Enum definition
2. `src/Domain/ValueObject/Identity/Actor.php` - Value object implementation
3. `tests/Unit/ValueObject/Identity/ActorTest.php` - Unit tests
4. `docs/examples/actor-value-object.md` - Usage examples

## Files to Modify

1. `README.md` - Add Actor to features list and examples
2. `docs/features/1005-actor-value-object.md` - Update status and add implementation addendum

---

## Verification Commands

```bash
# Run all quality checks
./utils quality

# Run specific tests
./utils shell
vendor/bin/phpunit tests/Unit/ValueObject/Identity/ActorTest.php

# Check code coverage
./utils test:coverage

# Run PHPStan on Actor files
./utils shell
vendor/bin/phpstan analyse src/Domain/ValueObject/Identity/Actor.php
vendor/bin/phpstan analyse src/Domain/ValueObject/Identity/ActorType.php
```

---

## Success Criteria

- ✅ All tasks in checklist completed
- ✅ All tests passing
- ✅ Code coverage ≥ 80%
- ✅ PHPStan level 8+ passes with no errors
- ✅ Code style (PSR-12) passes
- ✅ Documentation complete with examples
- ✅ Feature document updated with implementation addendum

---

## Implementation Notes

### Design Decisions

1. **Enum for Actor Type**: Using PHP 8.1+ backed enum for type safety
2. **Nullable Fields**: id, name, tenantId, branchId, sourceSystem are nullable to support different actor types
3. **Factory Methods**: Provide convenient factory methods for each actor type with sensible defaults
4. **Serialization**: Uses array format for easy JSON encoding in events
5. **Immutability**: Private constructor and readonly properties ensure immutability

### Common Pitfalls to Avoid

1. Don't allow empty strings - decide if they should be null or throw exception
2. Ensure ActorType enum values match exactly in serialization
3. Test edge cases with null values thoroughly
4. Verify CompareSameValueTrait works with nullable properties
5. Ensure PHPStan understands the enum type correctly

### Testing Strategy

- **Unit Tests**: Focus on individual methods and edge cases
- **Integration Tests**: Verify interaction with other value objects
- **Serialization Tests**: Critical for event sourcing use cases
- **Equality Tests**: Important for value object semantics

---

## Next Steps After Implementation

1. Consider adding validation rules (e.g., email format for user IDs)
2. Consider adding helper methods (e.g., `isUser()`, `isSystem()`)
3. Consider adding more factory methods for specific use cases
4. Integrate into existing domain events as needed
5. Create migration guide if replacing existing actor representations

---

**Created:** 2025-11-13  
**Ready for:** Immediate implementation  
**Depends on:** Feature 1000 (Test Infrastructure) should be completed first for proper testing
