# 1001 — `Money::getAmount()` / `getCurrency()` Return Value Objects, Not Primitives

**Type:** Improvement
**Status:** Rejected
**Severity:** Low
**Reported:** 2026-02-21
**Resolved:** 2026-02-21
**Affects:** `src/Domain/ValueObject/Money/Basic.php`

---

## Issue

Reported that `Money::getAmount()` and `Money::getCurrency()` return value objects (`Amount`, `Currency`) rather than primitive values (`int`, `string`). The reporter suggested this is confusing for developers who expect primitive values from getters, particularly when storing Money in database projections. The suggestion was to add documentation examples showing the correct pattern for persistence vs domain logic usage.

---

## Findings

Verified against `src/Domain/ValueObject/Money/Basic.php`:

- `getAmount()` returns `Amount` (aliased from `Dranzd\Common\Domain\ValueObject\Native\Number\Integer`) — a value object.
- `getCurrency()` returns `Currency` (aliased from `Dranzd\Common\Domain\ValueObject\Native\String\Literal`) — a value object.
- `toArray()` already returns primitives: `['amount' => int, 'currency' => string]`.
- `toNative()` is available on each property VO for scalar extraction.

This is consistent with every other composite value object in the library (e.g. `Quantity\Basic`, `Location\Address`, `File\File`). All properties are value objects; all expose `toNative()` and `toArray()` for persistence.

---

## Root Cause

Not a defect. This is the intentional DDD design of the library: all properties of value objects are themselves value objects. Primitives are only surfaced via `toNative()` (on scalar VOs) and `toArray()` (on composite VOs) — these methods exist specifically to support persistence and serialization. Consumers who expect primitive getters are unfamiliar with the value object pattern as implemented here.

---

## Recommended Action

No code change required. The design is correct and consistent across the library.

Documentation already covers `toArray()` in `docs/guides/built-in-value-objects.md`. No additional documentation is warranted beyond what exists — the pattern is uniform and self-consistent.

---

## Owner Response

**Decision:** Reject
**Notes:**

This is by design. `Amount` and `Currency` are value objects — that is the point of this library. Consumers do not use native values directly in domain logic; `toNative()` and `toArray()` exist precisely to support storage. The API is correct. No change needed.

---

## Resolution

**Resolved:** 2026-02-21
**Commit/PR:** N/A
**Summary:** Rejected as not applicable. The reported behaviour is intentional DDD design. No code or documentation change made.
