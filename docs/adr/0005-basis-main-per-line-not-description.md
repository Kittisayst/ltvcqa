# Revert: one BasisMain per criterion line, not a title+description bundle

**Status:** accepted
**Supersedes:** ADR 0003

ADR 0003 collapsed the legacy `basisName` bullet list into a single `BasisMain` per legacy `basisMainID`, with the first line as `title` and the remaining lines joined into a `description` field — reasoning that `tb_document.basisMainID` submits evidence once per group, so the bullets must be descriptive detail of one requirement, not separately submittable items.

That reasoning was correct about *submission* granularity but missed *tracking* granularity: staff need to see, per Indicator, how many of its BasisMains have evidence uploaded and which ones don't ("3 of 5 uploaded"). With ADR 0003's shape, an Indicator's multiple real criteria were invisible individually — hidden as unstructured lines inside one BasisMain's `description` — so this progress could not be reported.

## Considered Options

- **Keep title+description, add a UI-only checklist over the description lines** — considered first, but doesn't solve the actual need: progress must be reportable per BasisMain (the unit the rest of the system — Document, upload UI, resource tables — already tracks), not per line of a text field with no identity of its own.
- **One BasisMain per criterion line, `description` removed (chosen)** — every criterion becomes its own `BasisMain` row under the Indicator, exactly as before ADR 0003 (an Indicator has 2-5 BasisMains). This is a straight revert: the `description` column is dropped from the `basis_mains` migration (no production data yet), and `LegacyQaDataSeeder::seedBasisMains()` again creates one `BasisMain` per `\n`-separated line instead of collapsing them.

## Consequences

- `basis_mains.description` column removed; `BasisMain::$description` no longer exists.
- Legacy documents (`tb_document.basisMainID`, submitted once per old group) attach to the **first** line's `BasisMain` only when seeded — the raw legacy data doesn't record which line the original evidence bundle actually covered. The other lines seed with no historical Document attached, same as any newly-authored BasisMain.
- The standalone `BasisMainResource` (`admin/basis-mains`) and `QaFrameworkResource`'s copy-standards action both drop `description` from their form/table/copy logic.
- Per-Indicator upload-progress reporting ("N of M BasisMains uploaded") becomes possible again, since each criterion is once more its own row.
