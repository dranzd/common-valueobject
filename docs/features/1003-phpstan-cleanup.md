# 1003 - PHPStan Cleanup

**Status:** 🔵 Planned  
**Priority:** Medium  
**Series:** 1000 - Core Infrastructure & Testing  
**Dependencies:** 1000 (Test Infrastructure)

---

## Overview

Address PHPStan static analysis warnings and achieve level 8+ compliance. Currently, there are 16 warnings related to "Unsafe usage of new static()" in value object factory methods. This feature aims to resolve these warnings while maintaining code quality and type safety.

## Business Context

Static analysis is crucial for:
- Catching bugs before runtime
- Ensuring type safety across the codebase
- Maintaining code quality standards
- Providing confidence in refactoring
- Meeting professional library standards

PHPStan level 8+ compliance demonstrates commitment to code quality and helps prevent subtle bugs.

## Objectives

- Resolve all 16 "new static()" warnings
- Achieve PHPStan level 8+ compliance
- Maintain or improve type safety
- Document PHPStan configuration and exceptions
- Establish baseline for future development
- Integrate PHPStan into CI/CD pipeline

---

## Tasks

### 1. Analyze Current Warnings

- [ ] Review all 16 PHPStan warnings
- [ ] Categorize warnings by type and location
- [ ] Identify root causes
- [ ] Determine which warnings are legitimate vs. false positives
- [ ] Document each warning with context

### 2. Fix "new static()" Warnings

**Current Issue**: PHPStan flags `new static()` as unsafe in factory methods

**Options to resolve**:

#### Option A: Use self-typed factory methods
- [ ] Change return type from `static` to `self`
- [ ] Verify this doesn't break inheritance
- [ ] Test with extending classes

#### Option B: Add PHPStan annotations
- [ ] Add `@return static` annotations
- [ ] Use `@phpstan-return` for specific cases
- [ ] Document why annotations are needed

#### Option C: Refactor to named constructors
- [ ] Create specific factory methods
- [ ] Remove generic `fromNative()` where problematic
- [ ] Ensure backward compatibility

#### Option D: Use PHPStan baseline
- [ ] Create baseline for acceptable warnings
- [ ] Document why each baseline entry exists
- [ ] Plan to reduce baseline over time

### 3. Review Each Value Object

- [ ] **Native Types** (4 files)
  - [ ] Boolean.php
  - [ ] Integer.php
  - [ ] Real.php
  - [ ] Literal.php
- [ ] **Identity** (1 file)
  - [ ] Uuid.php
- [ ] **DateTime** (3 files)
  - [ ] Date.php
  - [ ] DateTime.php
  - [ ] Time.php
- [ ] **Money** (1 file)
  - [ ] Basic.php
- [ ] **Quantity** (1 file)
  - [ ] Basic.php
- [ ] **Location** (1 file)
  - [ ] Address.php
- [ ] **File** (1 file)
  - [ ] File.php
- [ ] **Collection** (1 file)
  - [ ] Collection.php
- [ ] **Search** (9 files)
  - [ ] Review all search components

### 4. PHPStan Configuration

- [ ] Review `phpstan.neon` configuration
- [ ] Set appropriate level (target: 8 or max)
- [ ] Configure paths to analyze
- [ ] Set up excludes if needed
- [ ] Configure extensions if needed
- [ ] Add custom rules if beneficial
- [ ] Document configuration decisions

### 5. Additional Static Analysis Improvements

- [ ] Fix any other warnings that appear
- [ ] Add missing type hints
- [ ] Add missing PHPDoc blocks
- [ ] Improve generic type annotations
- [ ] Add `@template` annotations where appropriate
- [ ] Use `@psalm` annotations if needed

### 6. Testing

- [ ] Verify all tests still pass after changes
- [ ] Add tests for any refactored code
- [ ] Test inheritance scenarios
- [ ] Verify type safety is maintained
- [ ] Test with different PHP versions (8.3+)

### 7. Documentation

- [ ] Document PHPStan configuration
- [ ] Explain any baseline entries
- [ ] Document type safety patterns used
- [ ] Create guide for maintaining PHPStan compliance
- [ ] Add PHPStan badge to README

### 8. CI/CD Integration

- [ ] Add PHPStan to GitHub Actions workflow
- [ ] Set up PHPStan to run on every PR
- [ ] Configure to fail build on errors
- [ ] Add PHPStan results to PR comments
- [ ] Set up caching for faster runs

---

## Acceptance Criteria

- [ ] All 16 "new static()" warnings are resolved
- [ ] PHPStan level 8+ passes without errors
- [ ] All tests still pass
- [ ] Type safety is maintained or improved
- [ ] PHPStan configuration is documented
- [ ] Any baseline entries are documented with justification
- [ ] PHPStan is integrated into CI/CD
- [ ] README includes PHPStan badge
- [ ] Developer guide includes PHPStan usage

---

## Example Solutions

### Option A: Self-typed Factory Method

**Before:**
```php
<?php

namespace Dranzd\Common\Domain\ValueObject\Native\String;

use Dranzd\Common\Domain\ValueObject\ValueObject;

final class Literal implements ValueObject
{
    private string $value;
    
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    public static function fromNative($value): static  // ⚠️ PHPStan warning
    {
        return new static($value);  // ⚠️ Unsafe usage of new static()
    }
    
    public function toNative(): string
    {
        return $this->value;
    }
}
```

**After (Option A):**
```php
<?php

namespace Dranzd\Common\Domain\ValueObject\Native\String;

use Dranzd\Common\Domain\ValueObject\ValueObject;

final class Literal implements ValueObject
{
    private string $value;
    
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    public static function fromNative($value): self  // ✅ Use self instead of static
    {
        return new self($value);  // ✅ Use new self()
    }
    
    public function toNative(): string
    {
        return $this->value;
    }
}
```

### Option B: PHPStan Annotation

**After (Option B):**
```php
<?php

namespace Dranzd\Common\Domain\ValueObject\Native\String;

use Dranzd\Common\Domain\ValueObject\ValueObject;

final class Literal implements ValueObject
{
    private string $value;
    
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    /**
     * @phpstan-return static
     */
    public static function fromNative($value): static
    {
        return new static($value);  // ✅ Suppressed with annotation
    }
    
    public function toNative(): string
    {
        return $this->value;
    }
}
```

### Option D: PHPStan Baseline

**phpstan-baseline.neon:**
```neon
parameters:
    ignoreErrors:
        -
            message: '#Unsafe usage of new static\(\)#'
            path: src/Domain/ValueObject/Native/String/Literal.php
            count: 1
        # ... other baseline entries
```

### Updated phpstan.neon Configuration

```neon
parameters:
    level: 8
    paths:
        - src
    excludePaths:
        - src/Domain/ValueObject/Exception/*
    
    # Uncomment if using baseline
    # includes:
    #     - phpstan-baseline.neon
    
    # Type coverage
    checkMissingIterableValueType: true
    checkGenericClassInNonGenericObjectType: true
    
    # Strict rules
    checkAlwaysTrueCheckTypeFunctionCall: true
    checkAlwaysTrueInstanceof: true
    checkAlwaysTrueStrictComparison: true
    checkExplicitMixedMissingReturn: true
    checkFunctionNameCase: true
    
    # Report unused
    reportUnmatchedIgnoredErrors: true
    
    # Custom rules
    # Add any custom rules here
```

---

## PHPStan Levels Explained

| Level | Description | Recommendation |
|-------|-------------|----------------|
| 0 | Basic checks | Too permissive |
| 1-3 | Increasing strictness | Good for legacy code |
| 4-6 | Recommended for most projects | Good baseline |
| 7 | Very strict | Recommended for libraries |
| 8 | Maximum strictness | **Target for this library** |
| 9 | Experimental | Consider for future |

---

## Decision Matrix

| Approach | Pros | Cons | Recommendation |
|----------|------|------|----------------|
| **Option A: Use `self`** | Clean, no warnings, type-safe | May limit inheritance | ✅ **Preferred** (classes are final) |
| **Option B: Annotations** | Maintains `static`, flexible | Relies on annotations | ⚠️ Use if inheritance needed |
| **Option C: Refactor** | Most type-safe | Breaking changes | ❌ Too disruptive |
| **Option D: Baseline** | Quick fix | Hides real issues | ❌ Last resort only |

**Recommended Approach**: **Option A** - Since all value objects are `final` classes, using `self` instead of `static` is safe and eliminates warnings without compromising functionality.

---

## Related Features

- **1000 - Test Infrastructure**: Ensures changes don't break functionality
- **1001 - Core Value Object Tests**: Validates type safety
- **1004 - CI/CD Pipeline**: Automates PHPStan checks

---

**Created:** November 9, 2025  
**Target Completion:** November 18, 2025  
**Estimated Effort:** 1-2 days
