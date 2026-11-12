# Contributing

Bug reports, fixes and well-scoped additions are welcome.

## Scope

This library provides a small contract for value objects and a handful of general-purpose
implementations. A new value object is a good fit when it is useful across unrelated domains and needs
no dependency beyond the ones already required. Domain-specific types belong in the application that
owns the domain, built on the base classes here.

If you are unsure, open an issue describing the use case before writing code.

## Setup

Requires PHP 8.3+ and Composer.

```bash
git clone https://github.com/<your-username>/common-valueobject.git
cd common-valueobject
composer install
```

Or, with Docker instead of a local PHP:

```bash
make install
make check
```

## Quality gates

CI runs the same commands, and all of them must pass:

| Command | What it checks |
|---|---|
| `composer test` | PHPUnit suite |
| `composer phpstan` | PHPStan at level 8, no ignored errors |
| `composer cs-check` | PSR-12 with a 120 character line limit (`composer cs-fix` repairs most issues) |
| `composer check` | all of the above |

CI also enforces at least 95% line coverage (`composer test:coverage` needs pcov or Xdebug).

## Backward compatibility

Applications extend these classes (`final class OrderId extends Uuid`) and implement these
interfaces, so the public and protected surface is the API:

- Do not rename, remove or narrow classes, methods, properties, parameters or return types.
- Do not add `final`, native property types on `protected` properties, or native types to existing
  untyped signatures; a subclass or implementation elsewhere would stop loading. Use PHPDoc instead.
- Value objects are immutable: operations return a new instance. (`Collection` is the one
  exception, because it extends Doctrine's `ArrayCollection`.)

A change that cannot follow these rules needs a major version and an entry in the changelog.

## Code conventions

- Invalid input throws `\InvalidArgumentException` with a message that names the problem.
- Docblocks carry what the type system cannot express (array shapes, generics, `@throws`),
  not a restatement of the signature.
- Every behaviour change comes with a test. Prefer data providers with named cases, and include a
  test with a subclass when the class is meant to be extended.

## Pull requests

1. Branch from `master`.
2. Keep the change focused; unrelated refactoring belongs in a separate pull request.
3. Add an entry under "Unreleased" in `CHANGELOG.md` for anything users can observe.
4. Update `README.md` and the relevant page in `docs/reference` when the public API changes.
5. Write commit messages in the imperative mood, explaining why when it is not obvious.

The project follows [Semantic Versioning](https://semver.org/). Changing documented behaviour,
signatures or thrown exception types is a breaking change and needs a major release. Correcting
behaviour that was a defect is not; such fixes are listed under "Fixed" in the changelog.

## Releasing

Maintainers release from `master`: add a dated section for the version to `CHANGELOG.md`, merge it,
then run the "Release" workflow with that version. It checks that CI passed on the commit, creates
the `v<version>` tag and publishes a GitHub release with that changelog section as its notes.

## Conduct

Be respectful and constructive: discuss the code, not the person, and assume good intent.

## Security

Please report vulnerabilities privately; see [SECURITY.md](SECURITY.md).
