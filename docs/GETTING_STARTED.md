# Getting Started with Development

Welcome to the `dranzd/common-valueobject` library development! This guide will help you understand the project structure, development process, and how to contribute.

## Quick Links

- **[README](../README.md)** - Library overview and usage
- **[Development Plan](DEVELOPMENT_PLAN.md)** - Project roadmap and timeline
- **[Feature Management](FEATURE_MANAGEMENT.md)** - How features are tracked
- **[Features Directory](features/)** - All feature specifications

## Project Overview

**dranzd/common-valueobject** is a framework-agnostic Domain-Driven Design (DDD) value object library providing common implementations and utilities for building domain models with immutable, self-validating value objects.

### Key Information

- **Language:** PHP 8.3+
- **Architecture:** Domain-Driven Design (DDD)
- **Testing:** PHPUnit 11
- **Static Analysis:** PHPStan Level 8+
- **Code Style:** PSR-12
- **License:** MIT

### Documentation Policy

⚠️ **Important:** All project documentation MUST be in the `docs/` directory (version controlled).

- ✅ **Use `docs/`** for: Features, plans, guides, examples, decisions
- ❌ **Use `.cascade/`** for: AI context only (NOT committed to git)

## Development Environment

### Prerequisites

- Docker and Docker Compose
- Git with SSH keys configured
- Text editor or IDE (PHPStorm, VSCode recommended)

### Setup

1. **Clone the repository**
   ```bash
   git clone git@github.com:dranzd/common-valueobject.git
   cd common-valueobject
   ```

2. **Start Docker environment**
   ```bash
   ./utils up
   ```

3. **Install dependencies**
   ```bash
   ./utils install
   ```

4. **Verify setup**
   ```bash
   ./utils test
   ./utils phpstan
   ./utils cs-check
   ```

### Using the Utils Script

The `utils` script provides convenient commands for development:

```bash
# Container management
./utils up          # Start containers
./utils down        # Stop containers
./utils rebuild     # Rebuild containers
./utils shell       # Open shell in container

# Composer commands
./utils install     # Install dependencies
./utils update      # Update dependencies
./utils composer    # Run any composer command

# Testing
./utils test        # Run all tests
./utils test:unit   # Run unit tests (when available)
./utils test:coverage  # Generate coverage report

# Code quality
./utils phpstan     # Run static analysis
./utils cs-check    # Check code style
./utils cs-fix      # Fix code style issues
./utils quality     # Run all quality checks

# Help
./utils             # Show all available commands
```

## Project Structure

```
common-valueobject/
├── docs/                           # Documentation
│   ├── features/                   # Feature specifications
│   │   ├── README.md              # Features overview
│   │   ├── 1000-*.md              # Individual features
│   │   └── ...
│   ├── guides/                     # User guides
│   ├── examples/                   # Code examples
│   ├── migration/                  # Migration documentation
│   ├── DEVELOPMENT_PLAN.md        # Project roadmap
│   ├── FEATURE_MANAGEMENT.md      # Feature process
│   └── GETTING_STARTED.md         # This file
├── src/
│   └── Domain/
│       └── ValueObject/            # All value objects
│           ├── Native/             # Native types
│           ├── Identity/           # UUID
│           ├── DateTime/           # Date/Time types
│           ├── Money/              # Money type
│           ├── Location/           # Address type
│           ├── Collection/         # Collection type
│           ├── Search/             # Search components
│           └── ...
├── tests/                          # Tests (to be created)
│   ├── Unit/                       # Unit tests
│   └── Integration/                # Integration tests
├── .github/                        # GitHub configuration
│   └── workflows/                  # CI/CD workflows (to be created)
├── composer.json                   # Dependencies
├── phpunit.xml                     # PHPUnit configuration
├── phpstan.neon                    # PHPStan configuration
├── phpcs.xml                       # Code style configuration
└── utils                           # Development utility script
```

## Development Workflow

### 1. Understanding the Roadmap

Review the [Development Plan](DEVELOPMENT_PLAN.md) to understand:
- Current project phase
- Upcoming features
- Priorities and timeline
- Success metrics

### 2. Working with Features

All work is organized into features. See [Feature Management](FEATURE_MANAGEMENT.md) for details.

**Current Priority Features:**
1. **1000 - Test Infrastructure** (Critical)
2. **1001 - Core Value Object Tests** (Critical)
3. **1002 - Advanced Value Object Tests** (High)
4. **1003 - PHPStan Cleanup** (Medium)
5. **1004 - CI/CD Pipeline** (High)
6. **1005 - Actor Value Object** (High)

### 3. Development Process

#### Starting a New Feature

1. **Review the feature document**
   ```bash
   cat docs/features/1000-test-infrastructure.md
   ```

2. **Update feature status to In Progress**
   ```markdown
   **Status:** 🟡 In Progress
   ```

3. **Create a feature branch**
   ```bash
   git checkout -b feature/1000-test-infrastructure
   ```

4. **Implement the feature**
   - Follow the task checklist
   - Check off tasks as you complete them
   - Commit regularly with clear messages

5. **Run quality checks**
   ```bash
   ./utils quality
   ```

6. **Update the feature document**
   - Check off completed tasks
   - Add implementation notes if needed

7. **Create a pull request**
   - Reference the feature number
   - Include testing evidence
   - Request review

#### Completing a Feature

1. **Verify acceptance criteria**
   - All tasks completed
   - All tests passing
   - Code quality checks passing
   - Documentation updated

2. **Update feature status**
   ```markdown
   **Status:** ✅ Completed
   ```

3. **Add Implementation Addendum**
   ```markdown
   ---
   
   ## Implementation Addendum
   
   **Date:** 2025-11-XX
   **Status:** ✅ Completed
   
   ### What Was Implemented
   - List of actual implementation
   
   ### Files Created/Modified
   - path/to/file.php
   
   ### Design Decisions
   - Key decisions made
   
   ### Next Steps
   - Follow-up work needed
   ```

4. **Merge and close**
   - Merge the pull request
   - Update the feature document
   - Close related issues

## Code Quality Standards

### Testing

- **Coverage:** Minimum 80% (Target: 90%+)
- **Framework:** PHPUnit 11
- **Types:** Unit tests, Integration tests
- **Run:** `./utils test`

### Static Analysis

- **Tool:** PHPStan
- **Level:** 8+ (Maximum strictness)
- **Run:** `./utils phpstan`

### Code Style

- **Standard:** PSR-12
- **Tool:** PHP_CodeSniffer
- **Check:** `./utils cs-check`
- **Fix:** `./utils cs-fix`

### Git Commits

Follow conventional commits format:

```
feat(1000): Add test infrastructure base classes
test(1001): Add UUID value object tests
fix(1003): Resolve PHPStan static warnings
docs(1000): Update testing documentation
chore: Update dependencies
```

## Common Tasks

### Running Tests

```bash
# Run all tests
./utils test

# Run with coverage
./utils test:coverage

# Run specific test file (when tests exist)
./utils shell
vendor/bin/phpunit tests/Unit/ValueObject/UuidTest.php
```

### Static Analysis

```bash
# Run PHPStan
./utils phpstan

# Run on specific directory
./utils shell
vendor/bin/phpstan analyse src/Domain/ValueObject/Native
```

### Code Style

```bash
# Check code style
./utils cs-check

# Fix code style issues
./utils cs-fix

# Check specific file
./utils shell
vendor/bin/phpcs src/Domain/ValueObject/Identity/Uuid.php
```

### Running All Quality Checks

```bash
# Run everything at once
./utils quality
```

## Creating New Value Objects

### Basic Value Object Template

```php
<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Custom;

use Dranzd\Common\Domain\ValueObject\ValueObject;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;

final class MyValueObject implements ValueObject
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
        // Add validation logic
        if (empty($value)) {
            throw new \InvalidArgumentException('Value cannot be empty');
        }
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}
```

### Value Object Checklist

- [ ] Implements `ValueObject` interface
- [ ] Uses `CompareSameValueTrait` for equality
- [ ] Private constructor
- [ ] Static `fromNative()` factory method
- [ ] Public `toNative()` method
- [ ] Validation in constructor
- [ ] Immutable (no setters)
- [ ] `__toString()` method
- [ ] PHPDoc comments
- [ ] Unit tests with 80%+ coverage

## Getting Help

### Documentation

- **User Guides:** `docs/guides/`
- **Examples:** `docs/examples/`
- **API Reference:** PHPDoc in source code

### Resources

- **DDD Patterns:** [Domain-Driven Design Reference](https://domainlanguage.com/ddd/reference/)
- **Value Objects:** [Martin Fowler's Article](https://martinfowler.com/bliki/ValueObject.html)
- **PHP Standards:** [PSR-12](https://www.php-fig.org/psr/psr-12/)

### Community

- **Issues:** GitHub Issues for bugs and feature requests
- **Discussions:** GitHub Discussions for questions
- **Contributing:** See CONTRIBUTING.md (to be created)

## Next Steps

### For New Contributors

1. ✅ Read this guide
2. ✅ Review the [Development Plan](DEVELOPMENT_PLAN.md)
3. ✅ Understand [Feature Management](FEATURE_MANAGEMENT.md)
4. ✅ Set up development environment
5. 🔵 Pick a feature to work on (start with 1000-1004)
6. 🔵 Create a feature branch
7. 🔵 Implement and test
8. 🔵 Submit a pull request

### Immediate Priorities

**Phase 2: Quality & Testing** (Current Focus)

1. **Feature 1000** - Test Infrastructure (Critical)
   - Set up PHPUnit configuration
   - Create base test classes
   - Configure coverage reporting

2. **Feature 1001** - Core Value Object Tests (Critical)
   - Test native types
   - Test identity (UUID)
   - Test datetime types
   - Test money and quantity

3. **Feature 1004** - CI/CD Pipeline (High)
   - GitHub Actions workflows
   - Automated testing
   - Code quality checks

4. **Feature 1003** - PHPStan Cleanup (Medium)
   - Fix "new static()" warnings
   - Achieve level 8+ compliance

## Tips for Success

### Best Practices

✅ **DO:**
- Write tests first (TDD)
- Keep commits small and focused
- Update documentation as you code
- Run quality checks before committing
- Ask questions when unclear
- Reference feature numbers in commits

❌ **DON'T:**
- Skip writing tests
- Commit code that doesn't pass quality checks
- Modify completed features without addendums
- Work on multiple features simultaneously
- Ignore PHPStan warnings
- Forget to update feature documents

### Performance Tips

- Use Docker caching for faster builds
- Run specific tests during development
- Use `./utils quality` before final commit
- Keep test data small and focused

### Debugging

```bash
# Open shell for debugging
./utils shell

# Run PHP interactively
php -a

# Check PHP version and extensions
php -v
php -m

# Validate composer.json
composer validate

# Clear caches
rm -rf vendor/
composer install
```

---

**Happy Coding! 🚀**

For questions or issues, please open a GitHub issue or discussion.

---

**Last Updated:** November 9, 2025
