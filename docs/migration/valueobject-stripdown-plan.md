# ValueObject Library Strip-Down Plan

## Overview
Transform the cloned `dranzd/common-event-sourcing` repository into a focused `dranzd/common-valueobject` library by removing all non-valueobject components (Event Sourcing, Messaging, CQRS, etc.) and keeping only ValueObject-related code.

## Current State Analysis
- **Repository**: Contains full event-sourcing library with multiple concerns
- **Target**: Extract only ValueObject components as per Step 3 of library-separation-plan.md
- **PHP Version**: Currently requires PHP 8.2, will update to PHP 8.3

## Dependencies to Keep
- **php**: ^8.3 (updated from 8.2)
- **ext-json**: * (for JSON serialization)
- **ramsey/uuid**: ^4.7 (used by Uuid value object)

## Dependencies to Add
- **dranzd/common-domain-assert**: Private library (git@github.com:dranzd/common-domain-assert.git)
  - Replaces the local Assert component
  - Provides domain assertion utilities

## Dependencies to Update (Dev)
- **phpunit/phpunit**: ^11.0 (latest for PHP 8.3)
- **phpstan/phpstan**: ^2.0 (latest stable)
- **squizlabs/php_codesniffer**: ^3.10 (latest)

---

## Task Breakdown

### ✅ Task 1: Update Project Metadata
**Status:** Pending Approval  
**Estimated Time:** 15 minutes

**Actions:**
- Update `composer.json`:
  - Change package name: `dranzd/common-event-sourcing` → `dranzd/common-valueobject`
  - Change description to focus on value objects
  - Update namespace: `Dranzd\\Common\\EventSourcing` → `Dranzd\\Common\\Domain\\ValueObject`
  - Update PHP requirement: `^8.2` → `^8.3`
  - Update dev dependencies to latest versions
  - Add repository configuration for private `dranzd/common-domain-assert` library
  - Add `dranzd/common-domain-assert` to require section
  - Remove `webmozart/assert` from require section (now handled by common-domain-assert)
- Update `README.md`:
  - Change title and description
  - Remove event sourcing examples
  - Add value object usage examples
  - Update installation instructions
- Update `phpunit.xml`:
  - Change test suite name to "Common ValueObject Tests"

**Files Modified:**
- `/media/dev/dranzd/common-valueobject/composer.json`
- `/media/dev/dranzd/common-valueobject/README.md`
- `/media/dev/dranzd/common-valueobject/phpunit.xml`

---

### ✅ Task 2: Remove Event Sourcing Components
**Status:** Pending Approval  
**Estimated Time:** 10 minutes

**Actions:**
- Delete entire `src/Domain/Event/` directory (6 files)
- Delete entire `src/Domain/EventSourcing/` directory (16 files)
- Delete corresponding test files:
  - `tests/Domain/Event/AbstractEventTest.php`
  - `tests/Domain/Event/EventUpcasterChainTest.php`
  - `tests/Domain/Event/VersionedEventTraitTest.php`
  - `tests/Domain/EventSourcing/AbstractAggregateEventTest.php`
  - `tests/Domain/EventSourcing/AggregateRootTraitTest.php`
  - `tests/Domain/EventSourcing/MetadataInjectorsTest.php`

**Directories to Delete:**
- `/media/dev/dranzd/common-valueobject/src/Domain/Event/`
- `/media/dev/dranzd/common-valueobject/src/Domain/EventSourcing/`
- `/media/dev/dranzd/common-valueobject/tests/Domain/Event/`
- `/media/dev/dranzd/common-valueobject/tests/Domain/EventSourcing/`

---

### ✅ Task 3: Remove Messaging Components
**Status:** Pending Approval  
**Estimated Time:** 10 minutes

**Actions:**
- Delete entire `src/Domain/Message/` directory (12 files)
  - Includes: Message.php, GenericMessage.php, Command.php, Query.php, Event.php
  - Includes: CausationCapable, CorrelationCapable, ActorCapable traits
- Delete corresponding test files:
  - `tests/Domain/Message/GenericMessageTest.php`
  - `tests/Domain/Message/CausationAndCorrelationCapableTest.php`

**Directories to Delete:**
- `/media/dev/dranzd/common-valueobject/src/Domain/Message/`
- `/media/dev/dranzd/common-valueobject/tests/Domain/Message/`

---

### ✅ Task 4: Remove Assert Components and Add Dependency
**Status:** Pending Approval  
**Estimated Time:** 10 minutes

**Actions:**
- **REMOVE** `src/Domain/Assert/` directory (1 file: Assertion.php)
- Add dependency on private `dranzd/common-domain-assert` library
- Update `composer.json` to include repository configuration for private library:
  ```json
  "repositories": [
      {
          "type": "vcs",
          "url": "git@github.com:dranzd/common-domain-assert.git"
      }
  ],
  "require": {
      "dranzd/common-domain-assert": "^1.0"
  }
  ```

**Directories to Delete:**
- `/media/dev/dranzd/common-valueobject/src/Domain/Assert/`

**Files Modified:**
- `/media/dev/dranzd/common-valueobject/composer.json`

---

### ✅ Task 5: Keep and Review ValueObject Components
**Status:** Pending Approval  
**Estimated Time:** 30 minutes

**Actions:**
- **KEEP** entire `src/Domain/ValueObject/` directory (30 files)
- Review each value object for:
  - Dependencies on removed components (Event, Message, EventSourcing)
  - PHP 8.3 compatibility
  - Proper implementation of ValueObject interface
- Verify all value objects are self-contained

**Files to Review (30 files):**
- Core interfaces and traits:
  - `ValueObject.php`
  - `ScalarValueObjectTranslatorCapable.php`
  - `ArrayValueObjectTranslatorCapable.php`
  - `CompareSameValueTrait.php`
  - `ToStringUsingObjectHashTrait.php`

- Native types:
  - `Native/Logic/Boolean.php`
  - `Native/Number/Integer.php`
  - `Native/Number/Real.php`
  - `Native/String/Literal.php`

- Identity:
  - `Identity/Uuid.php`

- DateTime:
  - `DateTime/Date.php`
  - `DateTime/DateTime.php`
  - `DateTime/Time.php`

- Money:
  - `Money/Basic.php`

- Quantity:
  - `Custom/Quantity/Basic.php`

- Location:
  - `Location/Address.php`

- File:
  - `File/File.php`

- Collection:
  - `Collection/Collection.php`

- Search (9 files):
  - `Search/Criteria.php`
  - `Search/CriteriaProviderTrait.php`
  - `Search/Filter.php`
  - `Search/FilterTrait.php`
  - `Search/Page.php`
  - `Search/Query.php`
  - `Search/Result.php`
  - `Search/Sorter.php`
  - `Search/SorterTrait.php`

- Exceptions:
  - `Exception/FromNativeValueIncapable.php`
  - `Exception/InvalidNativeArgument.php`
  - `Exception/ToNativeValueIncapable.php`

---

### ✅ Task 6: Review and Clean Util Directory
**Status:** Pending Approval  
**Estimated Time:** 20 minutes

**Actions:**
- Review `src/Util/` directory (7 files)
- Determine which utilities are needed for value objects
- Remove utilities specific to event sourcing/messaging
- Keep generic utilities that support value objects

**Files to Review:**
- List and analyze each file in `/media/dev/dranzd/common-valueobject/src/Util/`

**Decision Criteria:**
- Keep: Generic helpers, array utilities, string utilities
- Remove: Event-specific, message-specific, aggregate-specific utilities

---

### ✅ Task 7: Update Namespaces
**Status:** Pending Approval  
**Estimated Time:** 20 minutes

**Actions:**
- Update all remaining PHP files to use new namespace
- Change from: `Dranzd\Common\EventSourcing\Domain\ValueObject`
- Change to: `Dranzd\Common\Domain\ValueObject`
- Update namespace in:
  - All ValueObject files (30 files)
  - All Util files (7 files - to be migrated later)
  - All test files (if any remain)

**Files to Update:**
- All files in `src/Domain/ValueObject/` (30 files)
- All files in `src/Util/` (7 files)
- Keep directory structure: `src/Domain/ValueObject/` (no move needed)
- Update Util namespace: `Dranzd\Common\EventSourcing\Util` → `Dranzd\Common\Domain\Util`

---

### ✅ Task 8: Clean Up Empty Directories
**Status:** Pending Approval  
**Estimated Time:** 5 minutes

**Actions:**
- Remove empty `tests/Domain/` directory
- Keep DDD structure with Domain layer
- Final structure:
  ```
  src/
  ├── Domain/
  │   └── ValueObject/
  │       ├── Collection/
  │       ├── Custom/
  │       ├── DateTime/
  │       ├── Exception/
  │       ├── File/
  │       ├── Identity/
  │       ├── Location/
  │       ├── Money/
  │       ├── Native/
  │       ├── Search/
  │       └── ValueObject.php (interface)
  └── Util/ (to be migrated to separate library later)
  ```

**Actions:**
- Remove empty `tests/Domain/` directory
- Verify `src/Domain/ValueObject/` structure is intact

---

### ✅ Task 9: Create/Update Tests for ValueObjects
**Status:** Pending Approval  
**Estimated Time:** 45 minutes

**Actions:**
- Create test directory structure:
  ```
  tests/
  ├── ValueObject/
  │   ├── NativeTest.php
  │   ├── UuidTest.php
  │   ├── DateTimeTest.php
  │   ├── MoneyTest.php
  │   └── ... (tests for each value object)
  └── Assert/
      └── AssertionTest.php
  ```
- Write basic tests for each value object:
  - `fromNative()` method
  - `toNative()` method
  - `sameValueAs()` method
  - Validation logic
  - Edge cases

**Test Coverage Goals:**
- Minimum 80% code coverage
- All public methods tested
- All exceptions tested

---

### ✅ Task 10: Clean Up Documentation
**Status:** Pending Approval  
**Estimated Time:** 30 minutes

**Actions:**
- Review and update/remove files in `docs/` directory:
  - Remove event sourcing guides
  - Remove migration docs (or archive)
  - Keep/create value object usage guides
- Create new documentation:
  - `docs/guides/creating-value-objects.md`
  - `docs/guides/built-in-value-objects.md`
  - `docs/examples/custom-value-object-example.md`
- Update or remove:
  - `docs/examples/event-versioning-example.md` (REMOVE)
  - `docs/guides/event-patterns.md` (REMOVE)
  - `docs/reviews/domain-event-design-review.md` (REMOVE)
  - `docs/migration/namespace-migration.md` (ARCHIVE or REMOVE)
  - `docs/migration/library-separation-plan.md` (ARCHIVE)

---

### ✅ Task 11: Update CI/CD Configuration
**Status:** Pending Approval  
**Estimated Time:** 20 minutes

**Actions:**
- Update `.github/workflows/` if exists
- Configure GitHub Actions for:
  - PHP 8.3 testing
  - PHPUnit tests
  - PHPStan analysis (level 8+)
  - PHPCS code style checks
- Add badges to README.md

**Files to Update:**
- `.github/workflows/*.yml`

---

### ✅ Task 12: Create Comprehensive README
**Status:** Pending Approval  
**Estimated Time:** 45 minutes

**Actions:**
- Write new README.md with:
  - Clear description of value object library
  - Installation instructions
  - Quick start guide
  - List of built-in value objects
  - How to create custom value objects
  - Examples for each major value object type
  - API reference
  - Contributing guidelines
  - License information

**Sections to Include:**
1. Introduction
2. Installation
3. Quick Start
4. Built-in Value Objects
5. Creating Custom Value Objects
6. Validation with Assertions
7. Best Practices
8. API Reference
9. Testing
10. Contributing
11. License

---

### ✅ Task 13: Update Docker Configuration
**Status:** Pending Approval  
**Estimated Time:** 15 minutes

**Actions:**
- Update `Dockerfile` to use PHP 8.3
- Update `docker-compose.yml` if needed
- Ensure Docker setup works for development
- Test running tests in Docker

**Files to Update:**
- `/media/dev/dranzd/common-valueobject/Dockerfile`
- `/media/dev/dranzd/common-valueobject/docker-compose.yml`

---

### ✅ Task 14: Run Dependency Update
**Status:** Pending Approval  
**Estimated Time:** 10 minutes

**Actions:**
- Run `composer update` to install updated dependencies
- Verify all dependencies are compatible with PHP 8.3
- Check for any deprecation warnings
- Update `composer.lock`

**Commands:**
```bash
composer update
composer validate
```

---

### ✅ Task 15: Run Tests and Quality Checks
**Status:** Pending Approval  
**Estimated Time:** 20 minutes

**Actions:**
- Run full test suite: `composer test`
- Run PHPStan analysis: `composer phpstan`
- Run code style check: `composer cs-check`
- Fix any issues found
- Ensure all checks pass

**Quality Gates:**
- ✅ All tests passing
- ✅ PHPStan level 8+ passing
- ✅ PHPCS passing (PSR-12)
- ✅ No deprecation warnings

---

### ✅ Task 16: Final Cleanup and Verification
**Status:** Pending Approval  
**Estimated Time:** 20 minutes

**Actions:**
- Remove any remaining event-sourcing references
- Verify all imports are correct
- Check for unused dependencies
- Verify LICENSE file is correct (MIT)
- Create CHANGELOG.md for v1.0.0
- Tag release: `v1.0.0`

**Verification Checklist:**
- [ ] No references to "event sourcing" in code
- [ ] No references to "messaging" in code
- [ ] No references to "CQRS" in code
- [ ] All namespaces use `Dranzd\Common\ValueObject`
- [ ] All tests pass
- [ ] Documentation is complete
- [ ] README is accurate
- [ ] composer.json is correct
- [ ] LICENSE file exists

---

## Summary

**Total Tasks:** 16  
**Estimated Total Time:** 5-6 hours  
**Current Status:** Planning Phase - Awaiting Approval

### Task Execution Order
1. Task 1: Update Project Metadata (safe, reversible)
2. Task 2: Remove Event Sourcing Components
3. Task 3: Remove Messaging Components
4. Task 4: Review Assert Components
5. Task 5: Review ValueObject Components
6. Task 6: Review Util Directory
7. Task 8: Restructure Directory Layout
8. Task 7: Update Namespaces (after restructure)
9. Task 9: Create/Update Tests
10. Task 10: Clean Up Documentation
11. Task 13: Update Docker Configuration
12. Task 14: Run Dependency Update
13. Task 11: Update CI/CD Configuration
14. Task 12: Create Comprehensive README
15. Task 15: Run Tests and Quality Checks
16. Task 16: Final Cleanup and Verification

### Dependencies Between Tasks
- Tasks 2-3 can be done in parallel (both remove components)
- Task 4-6 should be done after 2-3 (review what remains)
- Task 8 must be done before Task 7 (restructure before namespace update)
- Task 9 depends on Task 7 (tests need correct namespaces)
- Task 14 should be done before Task 15 (update deps before testing)
- Task 16 is final verification

---

## Risk Assessment

### Low Risk Tasks (Safe to Execute)
- Task 1: Metadata updates (easily reversible)
- Task 10: Documentation cleanup
- Task 13: Docker updates

### Medium Risk Tasks (Require Review)
- Task 2-3: Component removal (verify no dependencies)
- Task 4-6: Component review (need to check dependencies)
- Task 8: Directory restructure (affects all imports)

### High Risk Tasks (Require Testing)
- Task 7: Namespace updates (affects all files)
- Task 9: Test creation (need to ensure coverage)
- Task 15: Quality checks (must pass before completion)

---

**Document Version:** 1.0  
**Created:** 2025-11-02  
**Last Updated:** 2025-11-02  
**Status:** Awaiting User Approval to Begin Execution
