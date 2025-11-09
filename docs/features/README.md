# Features Directory

This directory contains feature specifications for the `dranzd/common-valueobject` library. Each feature is documented in a separate markdown file following a standardized format.

## Quick Reference

### Active Features

| Number | Name | Status | Priority | Dependencies |
|--------|------|--------|----------|--------------|
| 1000 | [Test Infrastructure](1000-test-infrastructure.md) | 🔵 Planned | Critical | None |
| 1001 | [Core Value Object Tests](1001-core-value-object-tests.md) | 🔵 Planned | Critical | 1000 |
| 1002 | [Advanced Value Object Tests](1002-advanced-value-object-tests.md) | 🔵 Planned | High | 1000, 1001 |
| 1003 | [PHPStan Cleanup](1003-phpstan-cleanup.md) | 🔵 Planned | Medium | 1000 |
| 1004 | [CI/CD Pipeline](1004-cicd-pipeline.md) | 🔵 Planned | High | 1000, 1001 |

### Status Legend

- 🔵 **Planned** - Feature is defined but work hasn't started
- 🟡 **In Progress** - Feature is actively being developed
- ✅ **Completed** - Feature is fully implemented and tested
- ⚠️ **Blocked** - Feature is blocked by dependencies or issues
- ❌ **Cancelled** - Feature has been cancelled or deprecated

## Feature Series

### 1000-1999: Core Infrastructure & Testing
Foundation features for testing, quality assurance, and development infrastructure.

**Current Features:**
- **1000** - Test Infrastructure
- **1001** - Core Value Object Tests
- **1002** - Advanced Value Object Tests
- **1003** - PHPStan Cleanup
- **1004** - CI/CD Pipeline

**Planned:**
- Test coverage reporting
- Performance benchmarking
- Integration test framework

### 2000-2999: Additional Value Object Types
New value object implementations for common use cases.

**Planned:**
- Email value object
- Phone number value object
- URL value object
- Color value object
- Percentage value object
- Range value objects (Date, Number, Time)
- Geolocation value objects

### 3000-3999: Advanced Capabilities
Advanced features and utilities for value objects.

**Planned:**
- Validation enhancement
- Serialization formats (JSON, XML)
- Value object factory pattern
- Comparison utilities

### 5000-5999: Documentation & Examples
Comprehensive documentation and real-world examples.

**Planned:**
- Usage examples
- API documentation
- Migration guides
- Video tutorials

### 6000-6999: Performance & Optimization
Performance improvements and optimization features.

**Planned:**
- Performance benchmarks
- Memory optimization
- Caching strategies

### 7000-7999: Developer Experience
Tools and features to improve developer productivity.

**Planned:**
- IDE support and metadata
- Code generators
- Debug helpers

### 8000-8999: Community & Ecosystem
Community building and ecosystem integration.

**Planned:**
- Framework integrations (Laravel, Symfony)
- Community contributions
- Plugin system

## How to Use This Directory

### Finding Features

**By Status:**
```bash
# Find all planned features
grep -l "Status.*Planned" docs/features/*.md

# Find all in-progress features
grep -l "Status.*In Progress" docs/features/*.md

# Find all completed features
grep -l "Status.*Completed" docs/features/*.md
```

**By Priority:**
```bash
# Find all critical features
grep -l "Priority.*Critical" docs/features/*.md

# Find all high priority features
grep -l "Priority.*High" docs/features/*.md
```

**By Series:**
```bash
# Find all features in 1000 series
ls docs/features/1*.md
```

### Creating a New Feature

1. **Determine the next feature number**
   ```bash
   ls docs/features/ | tail -1
   # Use the next sequential number
   ```

2. **Create the feature file**
   ```bash
   touch docs/features/1005-new-feature-name.md
   ```

3. **Use the template structure**
   - Copy structure from an existing feature
   - Update all sections with relevant information
   - Set status to 🔵 Planned

4. **Commit the feature**
   ```bash
   git add docs/features/1005-new-feature-name.md
   git commit -m "feat: Add feature 1005 - New Feature Name"
   ```

### Working on a Feature

1. **Update status to 🟡 In Progress**
2. **Check off tasks as you complete them**
3. **Keep the original content intact**
4. **Commit progress regularly**

### Completing a Feature

1. **Verify all acceptance criteria are met**
2. **Update status to ✅ Completed**
3. **Add Implementation Addendum**
   - Completion date
   - What was implemented
   - Files created/modified
   - Design decisions
   - Next steps

4. **Commit the completion**
   ```bash
   git add docs/features/1005-new-feature-name.md
   git commit -m "feat: Complete feature 1005 - New Feature Name"
   ```

## Feature Template

```markdown
# NNNN - Feature Name

**Status:** 🔵 Planned | 🟡 In Progress | ✅ Completed | ⚠️ Blocked | ❌ Cancelled
**Priority:** Critical | High | Medium | Low
**Series:** NNNN - Series Name
**Dependencies:** List of feature numbers

---

## Overview
Brief description of the feature

## Business Context
Why this feature is needed

## Objectives
What this feature aims to accomplish

---

## Tasks
Detailed checklist of implementation tasks

---

## Acceptance Criteria
How to verify the feature is complete

---

## Example Usage
Code examples demonstrating the feature

---

## Related Features
Links to related feature documents

---

**Created:** YYYY-MM-DD
**Target Completion:** YYYY-MM-DD
**Estimated Effort:** X days
```

## Documentation

- **[Feature Management Process](../FEATURE_MANAGEMENT.md)** - Complete guide to managing features
- **[Development Plan](../DEVELOPMENT_PLAN.md)** - Overall project roadmap and planning

## Statistics

**Total Features:** 5
- 🔵 Planned: 5
- 🟡 In Progress: 0
- ✅ Completed: 0
- ⚠️ Blocked: 0
- ❌ Cancelled: 0

**By Priority:**
- Critical: 3 (1000, 1001, 1004)
- High: 2 (1002, 1004)
- Medium: 1 (1003)
- Low: 0

**By Series:**
- 1000-1999 (Core Infrastructure): 5 features

---

**Last Updated:** November 9, 2025
