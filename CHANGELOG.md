# Changelog

All notable changes to `dranzd/common-valueobject` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-03-27

Initial stable release of the Common ValueObject library — a framework-agnostic, DDD value object library for PHP 8.3+.

### Added

#### Core Infrastructure
- `ValueObject` interface — base contract for all value objects (`sameValueAs`, `__toString`)
- `CompareSameValueTrait` — equality comparison via `toNative()` values
- `ToStringUsingObjectHashTrait` — `__toString()` via `spl_object_hash()`
- `ScalarValueObjectTranslatorCapable` interface — `fromNative()` / `toNative()` for scalar VOs
- `ArrayValueObjectTranslatorCapable` interface — `fromArray()` / `toArray()` for composite VOs

#### Native Types
- `Native\Logic\Boolean` — boolean value object
- `Native\Number\Integer` — integer value object with arithmetic (`plus`, `minus`, `multiply`), comparisons (`isGreaterThan`, `isLessThan`, `isEqualTo`, etc.), and state checks (`isEmpty`, `isNegative`)
- `Native\Number\Real` — float/decimal value object
- `Native\String\Literal` — string value object with `isEmpty` check

#### Identity
- `Identity\Uuid` — UUID v4 value object (backed by `ramsey/uuid`)
- `Identity\Actor` — actor value object for audit trails and event sourcing (id, name, type, tenantId, branchId)
- `Identity\ActorType` — enum for actor types (user, system, service, anonymous)

#### DateTime
- `DateTime\Date` — date-only value object (`Y-m-d`)
- `DateTime\DateTime` — date and time value object (`Y-m-d H:i:s`)
- `DateTime\Time` — time-only value object (`H:i:s`)

#### Money
- `Money\Basic` — money value object with amount and currency
  - Arithmetic operations: `add`, `subtract`, `multiply` (immutable, returns new instance)
  - State checks: `isZero`, `isPositive`, `isNegative`
  - Comparisons: `isGreaterThan`, `isGreaterThanOrEqual`, `isLessThan`, `equals`
  - Currency mismatch guard: `assertSameCurrency` (throws `InvalidArgumentException`)
  - Serialization: `fromArray` / `toArray`
  - Null object: `ofNoValue()`
- `Money\Currency` — currency value object extending `Literal`
  - Static factories: `Currency::USD()`, `Currency::EUR()`, etc. (validated via `CurrencyCode` enum)
  - Named constructors: `fromCode(CurrencyCode)`, `fromString(string)`, `fromNative(string)`
- `Money\CurrencyCode` — PHP string-backed enum of 27 ISO 4217 currency codes (USD, EUR, GBP, JPY, CNY, PHP, SGD, AUD, CAD, CHF, HKD, NZD, KRW, THB, INR, MYR, IDR, VND, TWD, BRL, MXN, ZAR, SEK, NOK, DKK, AED, SAR)

#### Quantity
- `Custom\Quantity\Basic` — quantity value object with value and unit

#### Location
- `Location\Address` — address value object (company, name, line1, line2, city, state, zipCode, country)

#### File
- `File\File` — file metadata value object (label, name, path, type, size)

#### Collection
- `Collection\Collection` — generic collection value object

#### Search Components
- `Search\Criteria` — search criteria (filters + sorters)
- `Search\CriteriaProviderTrait` — trait for criteria-aware classes
- `Search\Filter` — search filter (field, value, operator)
- `Search\FilterTrait` — trait for filter-aware classes
- `Search\Page` — pagination (number, limit)
- `Search\Query` — complete search query (filters, sorters, pagination)
- `Search\Result` — search result wrapper (items, total)
- `Search\Sorter` — sort specification (field, direction)
- `Search\SorterTrait` — trait for sorter-aware classes

#### Exceptions
- `Exception\FromNativeValueIncapable`
- `Exception\InvalidNativeArgument`
- `Exception\ToNativeValueIncapable`

#### Testing
- 55 tests, 128 assertions — all passing on PHP 8.3
- Test coverage for: Uuid, Actor, Integer, Literal, Money (Basic, Currency, CurrencyCode)

#### Development Tooling
- Docker environment with PHP 8.3 (`Dockerfile`, `docker-compose.yml`)
- `utils` script — Laravel Sail-style Docker management (up, down, shell, test, phpstan, cs-check, quality)
- `Makefile` — convenient `make` targets mirroring `utils` commands
- GitHub Actions CI — PHP 8.3/8.4 matrix (tests, PHPStan, PHPCS)
- PHPStan static analysis configuration
- PHPCS / PSR-12 code style enforcement

#### Documentation
- README with quick-start guide, architecture overview, and usage examples
- `docs/guides/built-in-value-objects.md` — complete reference for all value objects
- `docs/guides/creating-value-objects.md` — guide for building custom value objects
- `docs/guides/github-actions-setup.md` — CI/CD setup for private dependencies
- `docs/DEVELOPMENT_PLAN.md` — project roadmap and phased plan
- `docs/FEATURE_MANAGEMENT.md` — feature tracking process
- `docs/GETTING_STARTED.md` — contributor onboarding
- `docs/examples/` — real-world value object examples
- `docs/migration/` — library extraction and namespace migration notes
- `tests/README.md` — test suite guide and conventions

### Dependencies

- **php** ^8.3
- **ext-json** *
- **ramsey/uuid** ^4.7
- **doctrine/collections** ^2.0
- **dranzd/common-domain-assert** dev-master
- **dranzd/common-utils** dev-master
- **phpunit/phpunit** ^11.0 (dev)
- **phpstan/phpstan** ^2.0 (dev)
- **squizlabs/php_codesniffer** ^3.10 (dev)
