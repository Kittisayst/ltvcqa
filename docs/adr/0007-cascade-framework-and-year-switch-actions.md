# Cascade the "publish" and "activate" one-click actions instead of blocking them

**Status:** accepted

ADR 0006's form-level guard (block un-publishing a framework the active `AcademicYear` depends on) combined with the pre-existing "only one framework published at a time" rule to create a deadlock: switching the system's current period from framework/year A to an already-existing framework/year B was impossible through the UI. Publishing B required A to become draft first; A becoming draft was blocked because the still-active year depending on it; and activating B's year required B's framework to already be published — circular.

Rather than relax either invariant (both "exactly one published framework" and "an active year's framework must stay published" are still correct on their own), the two dedicated one-click table actions were changed from blocking to cascading:

- **`QaFrameworksTable`'s "ເຜີຍແຜ່" (publish) action**: publishing framework B now automatically drafts any other currently-published framework A, and deactivates any `AcademicYear` that was using A.
- **`AcademicYearResource`'s "ຕັ້ງເປັນປັດຈຸບັນ" (activate) action**: activating year Y now automatically publishes Y's framework (if still draft) and drafts any other published framework, in addition to its existing behavior of deactivating every other year.

Both actions converge on the same end state (exactly one published framework, exactly one active year, and they match each other), so either one is a complete "make this the current period" switch. ADR 0006's guard is untouched and still applies to the manual single-field edit path (`QaFrameworkForm`'s status field) — it remains the safety net against an accidental one-field edit; these two actions are the deliberate, explicit way to perform the full switch.

## Consequences

- `app/Filament/Resources/QaFrameworks/Tables/QaFrameworksTable.php`'s `publish` action and `app/Filament/Resources/AcademicYears/AcademicYearResource.php`'s `activate` action each wrap their cascade in a `DB::transaction`.
- A framework with **multiple** `AcademicYear` rows is not fully handled by the `publish` cascade: it drafts the old framework and deactivates whichever of its years was active, but does not pick a new year to activate for the newly-published framework (ambiguous which one) — an admin must activate one explicitly via `AcademicYearResource`.
