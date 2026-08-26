# Block un-publishing the framework an active academic year depends on

**Status:** accepted

`AcademicYearResource` restricts the framework picker to `published` frameworks at creation time, but `QaFrameworkForm`'s existing "only one published framework at a time" rule only guarded the *publish* direction — switching a framework from `published` back to `draft` had no check at all. `UploadEvidence` (the department-facing evidence page) treats "current academic year's framework is not published" as its empty-state condition, so an admin un-publishing the framework behind the active academic year — by accident, with no warning — would blank that page for every department instantly.

Considered leaving this as an intentional "pause switch" for admins, but rejected it: there was no warning or confirmation step, so the blank page would look like a bug rather than a deliberate pause. Blocking the transition entirely, symmetrically with the existing publish-side rule, was chosen instead.

## Consequences

- `QaFrameworkForm`'s status rule now also fails validation when: the record is currently `published`, the incoming value is not `published`, and an `AcademicYear` with `is_active = true` still references it (`app/Filament/Resources/QaFrameworks/Schemas/QaFrameworkForm.php`).
- To retire a framework, the active academic year must be switched to a different (already-published) framework first.
- This check is form-layer only (consistent with the existing publish-side rule) — not a DB constraint or model observer — so it is bypassable via `tinker`/seeders/raw SQL, same gap as the original rule.
