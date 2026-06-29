# People (shared kernel)

The second module. Implements ADR-0001: a **Person** (master human record) is separate
from an **Enrolment** (one admission into one programme). One person has many enrolments,
so a returning NCE graduate keeps their identity and gains a second enrolment rather than
being duplicated.

## Tables

| Table | Purpose |
|---|---|
| `people` | Master human record. Required core: surname, first_name, gender, date_of_birth, phone. Everything else optional. Soft-deleted. |
| `enrolments` | The Student record. Owns `matric_number` (unique), `programme_type` (NCE/DEGREE), `entry_route`, `status`, graduation outcome. |

`programme_id`, `current_level_id`, and `subject_combination_id` on `enrolments` are nullable
placeholders — their foreign keys are added when the **Academics** module is built.

## The contract other modules use

Admissions and others depend on `PeopleDirectory`, resolved from the container:

```php
use Modules\People\Contracts\PeopleDirectory;

$matches = $directory->findPotentialMatches([
    'matric_number' => $input['nce_matric'] ?? null,
    'surname' => $input['surname'],
    'date_of_birth' => $input['dob'],
    'phone' => $input['phone'],
]);
// -> ranked candidates; a human officer confirms before linking. Never auto-merges.

$person = $directory->createPerson($attributes);
$directory->recordEnrolment($person, $enrolmentAttributes);
```

Bind it in the module service provider's `register()`:

```php
$this->app->bind(
    \Modules\People\Contracts\PeopleDirectory::class,
    \Modules\People\Services\PeopleDirectoryService::class,
);
```

## Matching (returning-graduate recognition)

`findPotentialMatches()` scores existing people strongest-first:
matric number (100) > phone (40) > date of birth (30) > surname (20) > first name (10),
surfacing anything at or above a threshold of 30. The system **suggests**; the admissions
officer **decides**. No silent merges.

## Invariants (tested)

- A person may hold many enrolments; the returning NCE→Degree path keeps one person.
- `matric_number` is unique and lives on the enrolment, never on the person.
- The matcher ranks a matric hit above a name-only hit.

## Run

```bash
php artisan module:migrate People
php artisan module:seed People
php artisan test --filter='PersonEnrolment|PeopleDirectory'
```
