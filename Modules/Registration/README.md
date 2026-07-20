# Registration

Per-session course registration. Where Academics' curriculum, Identity's session,
and Finance's clearance gate come together into a student's course form.

## Tables

| Table | Purpose |
|---|---|
| `registrations` | One per enrolment + session + semester. draft -> submitted -> approved. Snapshots `fee_cleared` and `total_units`. |
| `registration_courses` | The course-form lines; `is_carry_over` flags re-registered failures. |

## Decisions baked in

- **Fee clearance blocks at SUBMIT, not at open.** A draft can be built unpaid; `submit()`
  throws `FeeNotClearedException` unless `FeeClearance::isClearedToRegister()` passes.
- **Auto-fill required courses + carry-overs; student adds electives.** `openCourseForm()`
  pulls required curriculum courses and carry-overs; electives are added within add-drop.
- **Manual approval by default**, configurable to `auto` (approves on submit when validation
  passes) via `registration.approval_mode`.

## Swappable dependencies

- `CarryOverProvider` — carry-overs come from failed results. Bind
  `Modules\Assessments\Services\AssessmentsCarryOverProvider` (real) or
  `InterimCarryOverProvider` (none).
- Reads `AcademicStructure` (Academics) for the curriculum and `FeeClearance` (Finance) for the gate.

## Contract other modules use

```php
use Modules\Registration\Contracts\RegistrationDirectory;

$directory->isRegisteredFor($enrolmentId, $courseId, $sessionId, $semester); // Exams/Assessments gate
```

Bind in the module service provider's `register()`:

```php
$this->app->bind(\Modules\Registration\Contracts\CarryOverProvider::class, \Modules\Assessments\Services\AssessmentsCarryOverProvider::class);
$this->app->bind(\Modules\Registration\Contracts\RegistrationDirectory::class, \Modules\Registration\Services\RegistrationDirectoryService::class);
```

## Invariants (tested)

- Required courses auto-fill; electives do not. Carry-overs are included and flagged.
- Submission is blocked below the fee-clearance threshold; allowed at/above it.
- Manual mode leaves a submitted registration pending; auto mode approves on submit.

## Run

```bash
php artisan module:migrate Registration
php artisan module:seed Registration
php artisan test Modules/Registration/tests
```
