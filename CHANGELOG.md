# Changelog

All notable changes to `dranzd/common-valueobject` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.0] - 2026-11-12

### Added

- Support for `webmozart/assert` 2.x alongside 1.x (`^1.12 || ^2.0`).

### Changed

- `Uuid` validates the value itself instead of delegating to `webmozart/assert`, whose two major
  versions disagree on malformed input. The result is now the same whichever version is installed,
  and matches the stricter 2.x rule.

  Accepted, with hex digits in either case: the plain form, `{...}`, `urn:uuid:...`, `uuid:...` and
  `uuid:{...}`.

  No longer accepted, although 1.1.0 let them through and stored them as given:

  - an unbalanced, doubled or misplaced brace, such as `{550e8400-...` without the closing brace;
  - braces combined with the URN prefix (`urn:uuid:{...}`) or a prefix inside the braces
    (`{urn:uuid:...}`, `{uuid:...}`);
  - a repeated or partial prefix, such as `urn:urn:...` or `urn:...`.

  Well-formed UUIDs are unaffected. If identifiers were ever stored in one of the rejected forms,
  `new Uuid()` and `fromNative()` now throw for them, so check stored values that contain `{`, `}`
  or `:` before upgrading.

## [1.1.0] - 2026-11-12

First public release. No class, method, property or signature was removed or changed, so existing
subclasses and implementations keep working. One method was added, and the defects below were
fixed.

### Added

- `Money\Basic::isLessThanOrEqual()`, completing the set of comparisons. A subclass that already
  declares a method of that name needs a compatible signature.
- A test suite covering every class, PHPStan at level 8, and CI on PHP 8.3, 8.4 and 8.5.
- Reference documentation under `docs/reference`.

### Changed

- Assertions use `webmozart/assert` directly. The package no longer depends on
  `dranzd/common-domain-assert` or `dranzd/common-utils`, and no longer requires `ext-json`.
- `Collection` carries generic type information (`Collection<TKey, T>`) for static analysis.
  Subclasses without annotations are unaffected.
- `declare(strict_types=1)` is enabled in files where it cannot affect callers. The primitives
  (`Literal`, `Integer`, `Real`, `Boolean`), `Uuid` and `SorterTrait` deliberately stay without it,
  so `fromNative()` keeps PHP's scalar coercion.

### Fixed

`fromArray()` on `Money\Basic`, `Quantity\Basic`, `File` and `Address`:

- A positional array no longer raises `E_DEPRECATED: Use of "static" in callables is deprecated` on
  PHP 8.2 and later.
- A missing key throws `InvalidArgumentException` naming the key. It used to raise an "Undefined
  variable" warning and continue with an empty value.
- Too few positional elements throw `InvalidArgumentException` instead of a `ValueError`.
- A positional array whose first element is `null` is read positionally. It used to be mistaken for
  a keyed array.
- A positional array that also contains string keys is read by position instead of failing with
  "Unknown named parameter".

Other fixes:

- `Date`, `DateTime` and `Time` reject values that do not parse in their format, such as an empty
  string. The constructor used to run this check and ignore the result. The check itself is
  unchanged: it is PHP's lenient parser, so `2024-02-30` is still accepted.
- `Quantity\Basic` keeps fractional values: `fromArray([1.5, 'kg'])` used to be truncated to `1.0`.
  Values that are not finite numbers are rejected.
- `Money\Basic::equals()`, `add()`, `subtract()` and the comparisons accept a `Currency` subclass
  holding the same code. They used to report a currency mismatch.
- `Search\Query::filterBy()` works. A typo discarded the filters and broke `getFilters()`.
- `Search\CriteriaProviderTrait` treats an unassigned filter or sorter list as empty instead of
  raising a warning, and skips a registered class that returns something other than a filter or
  sorter.
- `Real::isEmpty()` returns `true` for `0.0`; it always returned `false`.
- `Boolean::__toString()` prints `true` or `false`; it printed `1` or an empty string.
- `Collection::hasValueObjectItem()` returns `false` for an argument that is not a value object
  instead of raising a `TypeError`.
- `CompareSameValueTrait` returns `false` when compared with something that is not a value object
  or cannot be converted, instead of raising an error.
- `Actor::fromArray()` throws `InvalidArgumentException` for an unknown actor type instead of
  letting a `ValueError` escape.
- `Exception\InvalidNativeArgument` can describe array and object values without raising an error
  of its own.

## [1.0.1] - 2026-06-19

### Changed

- Pinned `dranzd/common-domain-assert` and `dranzd/common-utils` to `^1.0` (their stable releases) instead of `dev-master`, so this package resolves under `minimum-stability: stable` for downstream consumers.
- Switched the `common-domain-assert` and `common-utils` VCS repository URLs from SSH (`git@github.com:`) to HTTPS, matching the rest of the package ecosystem.

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
