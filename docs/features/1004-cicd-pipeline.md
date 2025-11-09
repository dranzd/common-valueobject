# 1004 - CI/CD Pipeline

**Status:** 🔵 Planned  
**Priority:** High  
**Series:** 1000 - Core Infrastructure & Testing  
**Dependencies:** 1000 (Test Infrastructure), 1001 (Core Value Object Tests)

---

## Overview

Set up a comprehensive CI/CD pipeline using GitHub Actions to automate testing, code quality checks, and deployment processes. This ensures code quality is maintained and issues are caught early in the development cycle.

## Business Context

Automated CI/CD provides:
- **Quality Assurance**: Automated testing on every commit
- **Early Bug Detection**: Catch issues before they reach production
- **Consistency**: Same checks run for all contributors
- **Confidence**: Safe to merge with passing checks
- **Documentation**: Build status visible to users
- **Efficiency**: Automated processes save developer time

## Objectives

- Set up GitHub Actions workflows
- Automate PHPUnit test execution
- Automate PHPStan static analysis
- Automate PHPCS code style checks
- Generate and publish code coverage reports
- Add status badges to README
- Support multiple PHP versions
- Enable automated dependency updates

---

## Tasks

### 1. GitHub Actions Workflow Setup

- [ ] Create `.github/workflows/` directory
- [ ] Create main CI workflow file (`ci.yml`)
- [ ] Configure workflow triggers
  - [ ] On push to main branch
  - [ ] On pull requests
  - [ ] On manual dispatch
  - [ ] Scheduled runs (optional)
- [ ] Set up workflow permissions

### 2. Test Automation

- [ ] Configure PHPUnit job
  - [ ] Set up PHP environment
  - [ ] Install dependencies with Composer
  - [ ] Run PHPUnit tests
  - [ ] Upload test results
- [ ] Test matrix for multiple PHP versions
  - [ ] PHP 8.3
  - [ ] PHP 8.4 (when available)
- [ ] Configure test parallelization
- [ ] Set up test result reporting

### 3. Code Coverage

- [ ] Configure coverage generation
  - [ ] Use Xdebug or PCOV
  - [ ] Generate coverage reports
  - [ ] Upload to Codecov or Coveralls
- [ ] Set coverage thresholds
  - [ ] Minimum 80% coverage
  - [ ] Fail build if below threshold
- [ ] Add coverage badge to README
- [ ] Configure coverage comments on PRs

### 4. Static Analysis

- [ ] Configure PHPStan job
  - [ ] Set up PHP environment
  - [ ] Install dependencies
  - [ ] Run PHPStan analysis
  - [ ] Report results
- [ ] Set PHPStan level requirement (8+)
- [ ] Cache PHPStan results for speed
- [ ] Add PHPStan badge to README

### 5. Code Style Checks

- [ ] Configure PHPCS job
  - [ ] Set up PHP environment
  - [ ] Install dependencies
  - [ ] Run PHPCS checks
  - [ ] Report violations
- [ ] Use PSR-12 standard
- [ ] Configure auto-fix suggestions
- [ ] Add code style badge to README

### 6. Dependency Management

- [ ] Set up Dependabot
  - [ ] Configure `dependabot.yml`
  - [ ] Set update frequency
  - [ ] Configure auto-merge for minor updates
- [ ] Configure Composer audit
  - [ ] Check for security vulnerabilities
  - [ ] Fail build on high-severity issues
- [ ] Set up dependency caching
  - [ ] Cache Composer dependencies
  - [ ] Cache PHPStan results

### 7. Build Optimization

- [ ] Configure caching strategies
  - [ ] Composer cache
  - [ ] PHPStan cache
  - [ ] Test cache
- [ ] Optimize job execution order
  - [ ] Run fast checks first
  - [ ] Parallelize independent jobs
- [ ] Set reasonable timeouts
- [ ] Configure job dependencies

### 8. Notifications and Reporting

- [ ] Configure build status notifications
  - [ ] Email notifications (optional)
  - [ ] Slack notifications (optional)
- [ ] Set up PR comments
  - [ ] Test results
  - [ ] Coverage changes
  - [ ] Static analysis results
- [ ] Add status badges to README
  - [ ] Build status
  - [ ] Coverage percentage
  - [ ] PHPStan level
  - [ ] License

### 9. Security Scanning

- [ ] Set up security scanning
  - [ ] Composer audit for vulnerabilities
  - [ ] Code scanning (CodeQL)
  - [ ] Secret scanning
- [ ] Configure security alerts
- [ ] Set up automated security updates

### 10. Documentation

- [ ] Document CI/CD setup in README
- [ ] Create CONTRIBUTING.md with CI/CD info
- [ ] Document how to run checks locally
- [ ] Document badge meanings
- [ ] Create troubleshooting guide

---

## Acceptance Criteria

- [ ] GitHub Actions workflows are configured and working
- [ ] Tests run automatically on every push and PR
- [ ] PHPStan runs automatically with level 8+
- [ ] PHPCS runs automatically with PSR-12
- [ ] Code coverage is generated and reported
- [ ] Coverage threshold (80%) is enforced
- [ ] Multiple PHP versions are tested (8.3+)
- [ ] Status badges are added to README
- [ ] Dependabot is configured
- [ ] Security scanning is enabled
- [ ] Build times are optimized (<5 minutes)
- [ ] Documentation is complete

---

## Example Workflow

### Main CI Workflow (.github/workflows/ci.yml)

```yaml
name: CI

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]
  workflow_dispatch:

jobs:
  tests:
    name: Tests (PHP ${{ matrix.php }})
    runs-on: ubuntu-latest
    
    strategy:
      fail-fast: false
      matrix:
        php: ['8.3', '8.4']
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          extensions: json, mbstring
          coverage: xdebug
          tools: composer:v2
      
      - name: Get Composer cache directory
        id: composer-cache
        run: echo "dir=$(composer config cache-files-dir)" >> $GITHUB_OUTPUT
      
      - name: Cache Composer dependencies
        uses: actions/cache@v3
        with:
          path: ${{ steps.composer-cache.outputs.dir }}
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: ${{ runner.os }}-composer-
      
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress --no-interaction
      
      - name: Run tests
        run: composer test
      
      - name: Generate coverage report
        if: matrix.php == '8.3'
        run: composer test:coverage
      
      - name: Upload coverage to Codecov
        if: matrix.php == '8.3'
        uses: codecov/codecov-action@v3
        with:
          files: ./coverage.xml
          fail_ci_if_error: true

  phpstan:
    name: PHPStan
    runs-on: ubuntu-latest
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: json, mbstring
          tools: composer:v2
      
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress --no-interaction
      
      - name: Run PHPStan
        run: composer phpstan

  code-style:
    name: Code Style
    runs-on: ubuntu-latest
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          tools: composer:v2, cs2pr
      
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress --no-interaction
      
      - name: Run PHPCS
        run: composer cs-check -- --report=checkstyle | cs2pr

  security:
    name: Security Audit
    runs-on: ubuntu-latest
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          tools: composer:v2
      
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress --no-interaction
      
      - name: Run security audit
        run: composer audit
```

### Dependabot Configuration (.github/dependabot.yml)

```yaml
version: 2
updates:
  - package-ecosystem: "composer"
    directory: "/"
    schedule:
      interval: "weekly"
      day: "monday"
      time: "09:00"
    open-pull-requests-limit: 10
    reviewers:
      - "dranzd"
    labels:
      - "dependencies"
      - "composer"
    commit-message:
      prefix: "chore"
      include: "scope"
    
  - package-ecosystem: "github-actions"
    directory: "/"
    schedule:
      interval: "weekly"
      day: "monday"
      time: "09:00"
    open-pull-requests-limit: 5
    labels:
      - "dependencies"
      - "github-actions"
    commit-message:
      prefix: "chore"
```

### README Badges

```markdown
# Common ValueObject

[![CI](https://github.com/dranzd/common-valueobject/workflows/CI/badge.svg)](https://github.com/dranzd/common-valueobject/actions)
[![codecov](https://codecov.io/gh/dranzd/common-valueobject/branch/main/graph/badge.svg)](https://codecov.io/gh/dranzd/common-valueobject)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue.svg)](https://php.net)
```

---

## Workflow Jobs

### Job Execution Order

```
┌─────────────┐
│   Checkout  │
└──────┬──────┘
       │
       ├──────────────┬──────────────┬──────────────┐
       │              │              │              │
       ▼              ▼              ▼              ▼
┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐
│  Tests   │   │ PHPStan  │   │   PHPCS  │   │ Security │
│ (Matrix) │   │          │   │          │   │  Audit   │
└──────────┘   └──────────┘   └──────────┘   └──────────┘
       │
       ▼
┌──────────────┐
│   Coverage   │
│   Upload     │
└──────────────┘
```

### Job Dependencies

- **Tests**: No dependencies, runs first
- **PHPStan**: No dependencies, runs in parallel with tests
- **PHPCS**: No dependencies, runs in parallel with tests
- **Security**: No dependencies, runs in parallel with tests
- **Coverage Upload**: Depends on tests job

---

## Performance Optimization

### Caching Strategy

1. **Composer Dependencies**: Cache vendor directory
2. **PHPStan Results**: Cache result cache
3. **Test Results**: Cache for incremental runs

### Expected Build Times

- **Fast Path** (code style only): ~1 minute
- **Full Build** (all checks): ~3-5 minutes
- **With Coverage**: ~5-7 minutes

---

## Local Development

### Running CI Checks Locally

```bash
# Run all quality checks
composer quality

# Individual checks
composer test              # Run tests
composer test:coverage     # Generate coverage
composer phpstan           # Run static analysis
composer cs-check          # Check code style
composer cs-fix            # Fix code style
composer audit             # Security audit
```

### Act (Local GitHub Actions)

```bash
# Install act
brew install act

# Run CI locally
act -j tests
act -j phpstan
act -j code-style
```

---

## Related Features

- **1000 - Test Infrastructure**: Provides tests to run
- **1001 - Core Value Object Tests**: Tests executed by CI
- **1002 - Advanced Value Object Tests**: Tests executed by CI
- **1003 - PHPStan Cleanup**: Static analysis run by CI

---

**Created:** November 9, 2025  
**Target Completion:** November 22, 2025  
**Estimated Effort:** 2-3 days
