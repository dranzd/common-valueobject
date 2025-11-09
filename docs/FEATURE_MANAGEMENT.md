# Feature Management Process

## Overview

This document defines the process for managing features in the `dranzd/common-valueobject` library. Features are tracked using numbered markdown files in the `docs/features/` directory.

## Feature Numbering System

Features are numbered sequentially starting from **1000**, with each new feature receiving the next available number:

- **1000** - First feature
- **1001** - Second feature
- **1002** - Third feature
- And so on...

### Numbering Rules

1. **Sequential Assignment**: Features are numbered in the order they are created
2. **No Gaps**: Do not skip numbers
3. **No Reuse**: Once a number is assigned, it is never reused even if the feature is cancelled
4. **Start at 1000**: This provides room for system/infrastructure features (0-999) if needed later

## Feature File Format

Each feature is documented in a separate markdown file: `docs/features/NNNN-feature-name.md`

### File Naming Convention

```
docs/features/1000-value-object-base.md
docs/features/1001-native-types.md
docs/features/1002-datetime-support.md
```

- **NNNN**: 4-digit feature number (padded with zeros)
- **feature-name**: Kebab-case descriptive name
- **.md**: Markdown extension

## Feature Document Structure

Each feature document must include:

### Header Section

```markdown
# NNNN - Feature Name

**Status:** 🔵 Planned | 🟡 In Progress | ✅ Completed | ⚠️ Blocked | ❌ Cancelled
**Priority:** Critical | High | Medium | Low
**Series:** NNNN - Series Name (optional)
**Dependencies:** List of feature numbers this depends on

---
```

### Core Sections

1. **Overview** - Brief description of the feature
2. **Business Context** - Why this feature is needed
3. **Objectives** - What this feature aims to accomplish
4. **Tasks** - Detailed checklist of implementation tasks
5. **Acceptance Criteria** - How to verify the feature is complete
6. **Example Usage** - Code examples demonstrating the feature
7. **Related Features** - Links to related feature documents

### Optional Sections

- **Technical Design** - Architecture and design decisions
- **Migration Notes** - Breaking changes or upgrade instructions
- **Performance Considerations** - Performance implications
- **Security Considerations** - Security implications

## Feature Lifecycle

### 1. Planning Phase

- Create feature document with **🔵 Planned** status
- Define objectives and acceptance criteria
- Identify dependencies
- Estimate effort

### 2. Implementation Phase

- Update status to **🟡 In Progress**
- Check off tasks as they are completed
- Update the document with implementation notes
- Keep the main sections unchanged

### 3. Completion Phase

- Update status to **✅ Completed**
- Verify all acceptance criteria are met
- Add completion date
- Create **Implementation Addendum** section

### 4. Post-Completion Updates

**IMPORTANT**: Once a feature is marked as **✅ Completed**, the original content is **FROZEN**.

Any updates, changes, or additional information must be added as an **Addendum** at the bottom of the document.

#### Addendum Format

```markdown
---

## Implementation Addendum

**Date:** YYYY-MM-DD
**Status:** ✅ Completed

### What Was Implemented

- Detailed list of what was actually built
- Any deviations from the original plan
- Additional features or improvements

### Files Created/Modified

**Created:**
- path/to/file1.php
- path/to/file2.php

**Modified:**
- path/to/file3.php - Description of changes

### Design Decisions

- Key architectural decisions made during implementation
- Rationale for choosing specific approaches

### Next Steps

- Follow-up features or improvements
- Known limitations or future enhancements

---
```

## Feature Series

Features can be grouped into series for better organization:

### Series Format

```markdown
**Series:** 1000 - Core Value Objects
**Series:** 2000 - Advanced Types
**Series:** 3000 - Integration Features
```

### Recommended Series Structure

- **1000-1999**: Core Value Objects and Base Infrastructure
- **2000-2999**: Advanced Value Object Types
- **3000-3999**: Integration and Utilities
- **4000-4999**: Testing and Quality
- **5000-5999**: Documentation and Examples
- **6000-6999**: Performance and Optimization
- **7000-7999**: Developer Experience
- **8000-8999**: Migration and Compatibility
- **9000-9999**: Future/Experimental Features

## Status Indicators

| Status | Icon | Meaning |
|--------|------|---------|
| Planned | 🔵 | Feature is defined but work hasn't started |
| In Progress | 🟡 | Feature is actively being developed |
| Completed | ✅ | Feature is fully implemented and tested |
| Blocked | ⚠️ | Feature is blocked by dependencies or issues |
| Cancelled | ❌ | Feature has been cancelled or deprecated |

## Priority Levels

| Priority | When to Use |
|----------|-------------|
| **Critical** | Core functionality, blocking other features, security issues |
| **High** | Important features that add significant value |
| **Medium** | Nice-to-have features that improve the library |
| **Low** | Future enhancements, optimizations, polish |

## Workflow

### Creating a New Feature

1. **Determine the next feature number**
   ```bash
   # List existing features
   ls docs/features/
   # Use the next sequential number
   ```

2. **Create the feature file**
   ```bash
   # Example: Creating feature 1005
   touch docs/features/1005-new-feature-name.md
   ```

3. **Fill in the template**
   - Copy the structure from an existing feature
   - Update all sections with relevant information
   - Set status to 🔵 Planned

4. **Commit the feature document**
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
3. **Add Implementation Addendum with:**
   - Completion date
   - What was actually implemented
   - Files created/modified
   - Design decisions
   - Next steps

4. **Commit the completion**
   ```bash
   git add docs/features/1005-new-feature-name.md
   git commit -m "feat: Complete feature 1005 - New Feature Name"
   ```

### Updating a Completed Feature

**DO NOT modify the original content!**

Instead:

1. **Add a new Addendum section** with the current date
2. **Document the changes or updates**
3. **Explain why the update was needed**

Example:
```markdown
---

## Addendum: Performance Optimization

**Date:** 2025-11-15
**Type:** Enhancement

### Changes Made

- Optimized XYZ method for better performance
- Added caching layer

### Impact

- 50% performance improvement in benchmarks
- No breaking changes
```

## Best Practices

### DO

✅ Create features before starting implementation
✅ Keep feature documents up-to-date during development
✅ Use clear, descriptive feature names
✅ Include code examples in the feature document
✅ Link related features together
✅ Add addendums for post-completion updates
✅ Check off tasks as you complete them

### DON'T

❌ Modify completed feature documents (use addendums instead)
❌ Skip feature numbers
❌ Reuse feature numbers
❌ Create features without clear objectives
❌ Leave features in "In Progress" state indefinitely
❌ Delete feature documents (mark as cancelled instead)

## Feature Review Process

Before marking a feature as complete:

1. **Code Review**: All code has been reviewed
2. **Tests**: Unit tests written and passing (≥80% coverage)
3. **Documentation**: PHPDoc and usage examples complete
4. **Quality**: PHPStan and PHPCS passing
5. **Examples**: Working code examples provided
6. **Acceptance Criteria**: All criteria verified

## Finding Features

### By Status

```bash
# Find all planned features
grep -l "Status.*Planned" docs/features/*.md

# Find all in-progress features
grep -l "Status.*In Progress" docs/features/*.md

# Find all completed features
grep -l "Status.*Completed" docs/features/*.md
```

### By Priority

```bash
# Find all critical features
grep -l "Priority.*Critical" docs/features/*.md
```

### By Series

```bash
# Find all features in a series
grep -l "Series: 1000" docs/features/*.md
```

## Integration with Development

### Git Commit Messages

Reference feature numbers in commit messages:

```bash
git commit -m "feat(1005): Implement core functionality"
git commit -m "test(1005): Add unit tests"
git commit -m "docs(1005): Update usage examples"
```

### Pull Requests

- Link to the feature document in PR description
- Update feature status in the same PR
- Check off completed tasks

### Issue Tracking

- Create GitHub issues for bugs found during feature development
- Reference the feature number in issue titles
- Link issues to feature documents

## Example Feature Document

See `docs/features/1000-value-object-base.md` for a complete example following this format.

---

**Document Version:** 1.0
**Created:** November 9, 2025
**Last Updated:** November 9, 2025
