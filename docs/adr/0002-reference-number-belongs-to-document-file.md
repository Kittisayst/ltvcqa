# Reference number and issue date belong to DocumentFile, not Document

**Status:** accepted

`documents.reference_no` and `documents.issued_date` were originally modeled on the submission container (`Document`), one value per submission. The legacy `tb_document` table confirms this was wrong from the start: it has no `fileNo`/`fileDate` columns at all — only `userID`, `basisMainID`, `yearID`. The reference number and issue date live exclusively on `tb_file`, one per physical file, and two files under the same legacy `documentID` can carry different reference numbers (each is a separately-numbered paper in the institution's filing system).

Because the initial migration collapsed this to one `reference_no`/`issued_date` per `Document` (taking only the first file's values), every legacy submission with more than one file silently lost the true reference numbers of its other files.

## Considered Options

- **Keep `reference_no`/`issued_date` on `Document` (status quo)** — rejected: contradicts the source data's own structure and loses information for any submission with more than one file.
- **Move `reference_no`/`issued_date` to `DocumentFile`, required (chosen)** — matches the legacy structure exactly: every physical file is a separately-numbered paper, so every `DocumentFile` requires its own reference number and issue date. `Document` remains a pure container (user + basis_main + academic_year) with no identity fields of its own.
- **Rename `Document` → `Submission` and `DocumentFile` → `Document`** — rejected for now: correctly reflects that "ເອກະສານ" colloquially means the numbered paper (i.e., today's `DocumentFile`), but would touch every already-built resource, label, and test that names the submission container "ເອກະສານ" (`DocumentResource`, navigation, forms). Revisit if the container/file distinction becomes confusing in practice.

## Consequences

- `document_files.reference_no` and `document_files.issued_date` are `NOT NULL`; `documents.reference_no`/`documents.issued_date` are dropped entirely.
- No uniqueness constraint on `reference_no` — it is issued by an external paper-filing process the app doesn't control, and the same number can legitimately recur across different issuing bodies.
- `DocumentsTable` shows reference numbers as a badge list sourced from `files.reference_no` (a `Document` can have zero-to-many, not one) instead of a single searchable column.
- Existing seeded (non-production) data was recovered by fixing `LegacyQaDataSeeder` to read `fileNo`/`fileDate` per row of `tb_file` directly into `document_files`, then re-running `migrate:fresh --seed` — recovering the true per-file values instead of attempting a lossy backfill from the already-collapsed `documents.reference_no`.
