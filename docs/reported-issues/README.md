# Reported Issues — Standards & Index

This directory contains all reported issues, improvements, and architectural concerns for the `dranzd/common-valueobject` library. It is the single source of truth for tracking problems discovered during development, integration, or review.

---

## Open Issues

See **[open-issues.md](open-issues.md)** for the current checklist of all unresolved issues, ordered by severity. Update that file when issues are resolved — not this one.

---

## Directory Structure

Issues are grouped by domain area, mirroring the feature series numbering used in `docs/features/README.md`. Each group is a subdirectory. Each issue is a single Markdown file prefixed with a 4-digit incremental number.

```
docs/reported-issues/
├── README.md                          ← this file (standards reference)
├── open-issues.md                     ← living checklist of unresolved issues
├── 1000-core/                         ← Core value object design and contracts
├── 2000-value-objects/                ← Built-in value object implementations
├── 3000-advanced/                     ← Advanced capabilities (collections, traits, etc.)
├── 5000-documentation/                ← Documentation and examples
├── 6000-performance/                  ← Performance and optimisation
└── 7000-dx/                           ← Developer experience
```

### Group Series

| Series | Group Name | Matches Feature Series |
|--------|-----------|----------------------|
| 1000 | Core Infrastructure & Testing | 1000 |
| 2000 | Additional Value Object Types | 2000 |
| 3000 | Advanced Capabilities | 3000 |
| 5000 | Documentation & Examples | 5000 |
| 6000 | Performance & Optimisation | 6000 |
| 7000 | Developer Experience | 7000 |

If an issue spans multiple groups, place it in the group of the **primary affected component**.

---

## Issue File Naming

```
NNNN-short-kebab-case-title.md
```

- `NNNN` — 4-digit number, unique within the entire `reported-issues/` tree (not just within the group folder). Start from the group series number (e.g., first issue in 6000 series = `6001`).
- Short title — lowercase kebab-case, describes the problem, not the solution.

**Examples:**
- `6001-convert-soft-reservation-to-hard.md`
- `8001-multi-terminal-enforcement-in-memory.md`

---

## Issue File Template

Every issue file must follow this exact template. Do not omit any section.

```markdown
# NNNN — Short Human-Readable Title

**Type:** Bug | Improvement | Missing Feature | Architecture
**Status:** Open | In Review | Resolved | Rejected
**Severity:** Critical | High | Medium | Low
**Reported:** YYYY-MM-DD
**Resolved:** YYYY-MM-DD (leave blank if open)
**Affects:** list of affected files or components

---

## Issue

Clear description of the problem as reported or observed. State what is wrong or missing, not the solution.

---

## Findings

Detailed investigation results. Include:
- Exact file paths and line references where the problem exists
- Code snippets where helpful
- Confirmation of whether the issue is valid, invalid, or broader than reported
- Any related components discovered during investigation

---

## Root Cause

Single concise paragraph explaining **why** the problem exists. Focus on the underlying design decision or gap, not the symptom.

---

## Recommended Action

Specific, actionable recommendation(s). May include options (Option A / Option B) with a preferred option stated. List all files that would need to change.

---

## Owner Response

> _(Owner fills in this section before implementation begins)_

**Decision:** Accept | Reject | Defer | Needs Discussion
**Preferred Option:** _(if multiple options were given)_
**Notes:**

_(Free-form space for the owner to record their preferred approach, constraints, or additional context before work begins.)_

---

## Resolution

_(Filled in when resolved)_

**Resolved:** YYYY-MM-DD
**Commit/PR:** link or reference
**Summary:** Brief description of what was done.
```

---

## Resolution Workflow

Every issue fix follows this workflow. See `.windsurf/workflows/branch-protection.md` for the full branch protection rules.

### 1 — Branch

Before writing any code, create a dedicated branch:

```
git checkout -b fix/<issue-id>-<short-description>
```

Example: `fix/8001-multi-terminal-stateless`

### 2 — Implement & Document

- Make all code changes
- Update the issue file: set `**Status:** Resolved`, `**Resolved:** YYYY-MM-DD`, fill `## Resolution`
- Remove the issue from `open-issues.md`

### 3 — Commit

Once all tests pass, commit with this structured message format:

```
fix(<issue-id>): <short human-readable description>

Resolves reported issue #<issue-id>.
<One or two sentences describing what changed and why.>
```

Example:

```
fix(8001): make MultiTerminalEnforcementService stateless

Resolves reported issue #8001.
Removed in-memory state arrays and mutation methods. Assert methods now
accept read-model-sourced state as arguments so invariants are enforced
across HTTP requests and processes.
```

### 4 — Confirm

Run the full test suite. All tests must pass before a merge is suggested.

### 5 — Merge Suggestion

After owner confirmation, the following merge commands are suggested (owner executes):

```
git checkout main
git merge --no-ff fix/<issue-id>-<short-description> -m "fix(<issue-id>): <description>"
git branch -d fix/<issue-id>-<short-description>
```

The merge is **never executed automatically** — it is always presented as a suggestion for the owner to approve.

---

## Status Values

| Status | Meaning |
|--------|---------|
| **Open** | Reported, not yet reviewed or acted on |
| **In Review** | Under active investigation |
| **Resolved** | Fix implemented and verified |
| **Rejected** | Determined not to be a valid issue or not worth fixing |

## Severity Values

| Severity | Meaning |
|----------|---------|
| **Critical** | Broken in production; invariants unenforced; data loss risk |
| **High** | Feature incomplete or incorrect; blocks integration |
| **Medium** | Naming/API inconsistency; adapter is no-op; misleading design |
| **Low** | Style, convention, or minor clarity issue |

---

## Issue Index

| ID | Group | Title | Type | Severity | Status | Reported |
|----|-------|-------|------|----------|--------|----------|
| [1001](1000-core/1001-money-getter-returns-vo-not-primitive.md) | Core | `Money::getAmount()` / `getCurrency()` return VOs — consumer confusion | Improvement | Low | Rejected | 2026-02-21 |

---

**Last Updated:** 2026-02-21
