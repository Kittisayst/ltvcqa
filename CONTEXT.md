# LTVCQA (Quality Assurance System)

Manages internal quality-assurance self-assessment for a vocational education institution: departments submit evidence documents against a ministry-issued QA framework, organized as Standard → Indicator → BasisMain.

## Language

**QA Framework (ຊຸດມາດຕະຖານ)**:
A complete, versioned bundle of Standards, Indicators, BasisMains, and BasisItems issued by the ministry (ກະຊວງ) and adopted nationally. Replaced wholesale on a periodic basis (not per-institution customization, not incremental edits to individual criteria) — when a new framework is issued, it supersedes the prior one for all institutions going forward. Exists independently of any AcademicYear, so it can be authored in **draft** before being adopted. Has a lifecycle: **draft** (being authored, not yet in use by any AcademicYear) → **published** (adopted, exactly one framework is published/active at a time system-wide). A published AcademicYear permanently references the framework that was active during it, for historical fidelity.
_Avoid_: version, edition (when referring to the whole bundle)

**BasisMain (ຫຼັກຖານ)**:
The unit of evidence a department must submit against, one level below Indicator. `title` is its only content — one short, self-contained criterion (e.g. "ມີວິໄສທັດ ແລະ ພາລະກິດ"). An Indicator typically has 2-5 BasisMains. Each is tracked and reported on individually — upload progress is measured per BasisMain within an Indicator ("3 of 5 uploaded"), which is the reason a BasisMain is never a multi-line bundle of several criteria. A Document is created per (department, BasisMain, AcademicYear).
_Avoid_: BasisItem, sub-criteria, checklist item, description (as a field bundling multiple criteria into one BasisMain — each criterion gets its own BasisMain row instead)

**Document (ເອກະສານ)**:
A submission — one department's evidence bundle for a single BasisMain, in a single AcademicYear. Uniquely identified by **department + basis_main + academic_year** (via the submitting user's `department_id`, not the user themselves) — at most one Document may exist per combination, system-wide, regardless of which staff member in the department creates or edits it. `user_id` records who submitted it, but is not part of the submission's identity: any staff member in the same department edits the same Document rather than creating a second one. It is a container: it groups one or more DocumentFiles but carries no reference number or date of its own — those belong to each individual file, not the submission as a whole.
_Avoid_: reference_no, issued_date, ເລກທີ່ (as a Document-level concept — these belong to DocumentFile); treating `user_id` as part of a Document's identity (it isn't — `department_id` is)

**DocumentFile (ໄຟລ໌ແນບ)**:
One physical evidentiary file (PDF/image/etc.) attached to a Document. Each DocumentFile is itself an official paper with its own reference number (`reference_no`, ເລກທີ່) and issue date (`issued_date`) — two files under the same Document can carry different reference numbers, because in the source institution's paper trail, every physical document that gets filed is separately numbered. A Document with multiple files is not one numbered paper; it's a bundle of several.
