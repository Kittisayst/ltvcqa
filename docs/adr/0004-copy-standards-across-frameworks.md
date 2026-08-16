# Copy standards/indicators/basis_mains across frameworks, by value

**Status:** accepted

Building a new academic year's framework from scratch means re-entering every Standard, Indicator, and BasisMain by hand, even when most of the criteria are unchanged from the prior framework. Staff asked for a way to reuse unchanged criteria when authoring the next framework.

This does not relax ADR 0001's "wholesale replacement" model: a Standard still belongs to exactly one framework (`framework_id` stays a single FK, no pivot table). "Reuse" means **copying by value** — a new, independent row is created for the target framework, with its own id and its own `framework_id`. Editing the copy never touches the source, so a published framework's historical fidelity (ADR 0001) is unaffected by later edits made while building the next one.

## Considered Options

- **Many-to-many pivot between frameworks and standards (true sharing)** — rejected: editing a shared row from a new framework would silently change what a past, published framework displays, violating the historical-fidelity guarantee ADR 0001 exists to protect.
- **Copy by value (chosen)** — a `copyFrom` row action on `QaFrameworkResource`, restricted to `draft` target frameworks only. Admin picks a source framework (draft or published) and selects whole Standards (with all their Indicators and BasisMains) to copy. Selection is Standard-level only — no partial (per-Indicator) selection — since that granularity was not needed and adds UI/logic complexity; an admin can delete unwanted Indicators from the copy afterward via the existing `IndicatorResource`.

## Consequences

- `standards` gets a `unique(['framework_id', 'name'])` DB constraint (added directly to the original migration, no production data yet) — a real invariant independent of this feature (no two standards with the same name within one framework), and also what makes the copy picker's "already copied" filtering correct.
- The `standard_ids` checkbox list in the copy action excludes any source standard whose `name` already exists in the target framework, rather than allowing the pick and failing on save.
- Target framework must have `status = 'draft'`; the action is hidden on `published` frameworks to prevent mutating a framework that may already be referenced by an `AcademicYear`.
