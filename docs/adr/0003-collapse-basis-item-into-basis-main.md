# Collapse BasisItem into BasisMain

**Status:** accepted

`basis_items` was introduced in the same refactor as ADR 0001 (`qa_frameworks`), by parsing each `tb_basismain.basisName` legacy blob — a `\n`-separated bullet list — into one `BasisMain` (title = first line) plus one `BasisItem` per line, including the first line again. This meant every `BasisMain`'s first `BasisItem` always duplicated its own `title`.

The legacy schema never had two levels: `tb_basismain` has no child table, and `tb_document.basisMainID` references a `tb_basismain` row directly — evidence is submitted once per `basisMainID`, not once per bullet line. The bullets are descriptive detail of a single evidence requirement (what it must demonstrate), not independently submittable items. Splitting them into separate `BasisMain` rows (one option considered) would have multiplied submission points per original criterion and forced departments to upload evidence separately for what was always one requirement.

## Considered Options

- **Keep the Main/Item split, fix the duplicate-first-item bug** — rejected: the two-level split has no basis in the source data or the submission model; fixing the duplication bug alone still leaves a fake hierarchy level.
- **Flatten every bullet line into its own `BasisMain`** — rejected: contradicts `tb_document.basisMainID` granularity: the legacy system submits one evidence bundle per `basisMainID`, not per bullet.
- **Collapse `BasisItem` into `BasisMain.description` (chosen)** — one `BasisMain` row per legacy `basisMainID`, matching submission granularity exactly. `title` = first line (short heading), `description` = remaining lines joined by `\n` (nullable), preserving the descriptive detail without inventing a submission-level entity that never existed.

## Consequences

- `basis_items` table, `BasisItem` model, and `BasisItemFactory` removed entirely. The `create_basis_items_table` migration is deleted (not superseded) since the app has no production data yet.
- `basis_mains` migration gains `description` (nullable text) directly, rather than via a follow-up migration.
- `LegacyQaDataSeeder::seedBasisMainsAndItems()` renamed to `seedBasisMains()`; `splitBasisItems()` renamed to `splitBasisName()` and now returns `[title, description]` instead of a flat list of items.
- `BasisMainsRelationManager` gains a `description` textarea/column; the `items_count` column (which counted the now-removed `items` relation) is replaced with the `description` column.
