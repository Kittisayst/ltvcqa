# QA Framework as a versioned entity, decoupled from AcademicYear

**Status:** accepted

Standards, Indicators, BasisMains, and BasisItems were originally modeled as global, year-agnostic master data — only `documents` and `reports` referenced `academic_year_id`. In reality, the ministry replaces the entire criteria set wholesale on an irregular schedule (not every academic year, and not per-institution), and historical reports must keep displaying the exact criteria that were in effect for the academic year they belong to, even after a newer framework is adopted.

We introduce a `qa_frameworks` entity, independent of `AcademicYear`, with a `standard_id`-rooted chain (`standards.framework_id → indicators → basis_mains → basis_items`, framework inherited through the chain rather than duplicated on every table). `academic_years.framework_id` is a many-to-one reference, set once by an administrator when the academic year is created and immutable afterward, so old academic years permanently keep the framework that was active during them.

## Considered Options

- **Global tables (status quo)** — rejected: cannot represent two different criteria sets existing at once, and offers no historical fidelity once a framework changes.
- **`academic_year_id` directly on `standards`** — rejected: duplicates the full Standard→Indicator→BasisMain→BasisItem tree on every academic year even when the framework hasn't changed, since consecutive academic years commonly share one framework for multiple years.
- **Separate `qa_frameworks` entity (chosen)** — normalized, allows drafting a new framework before any academic year adopts it, and lets multiple academic years share one framework record.

## Consequences

- `qa_frameworks` has a lifecycle: `draft` (authored, not yet adopted by any academic year) → `published` (exactly one framework is published/active system-wide at a time).
- `documents.basis_main_id` and `reports.indicator_id` must belong to the same framework as their `academic_year_id`. MySQL cannot express this as a single cross-table FK constraint, so it is enforced at the application/form layer (scoped select options + validation), not the database layer — this is a deliberate gap to be aware of when writing raw queries or seeders against these tables.
- Existing seeded legacy data (10 standards, 45 indicators, 45 basis_mains, academic_year "2023-2024") needs a one-time backfill: create a `qa_frameworks` row (placeholder name "ຊຸດມາດຕະຖານ 2023", status `published`), set it on all existing `standards` rows and on the "2023-2024" `academic_years` row.
