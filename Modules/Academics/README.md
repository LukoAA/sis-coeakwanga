# Academics

Schools, departments, programmes, subjects, courses, the level schemes, and the
curriculum. This module supplies the academic structure every other module reads.

## Tables

| Table | Purpose |
|---|---|
| `schools` -> `departments` -> `programmes` | Organisational hierarchy. Programmes carry `programme_type` (NCE/DEGREE). |
| `subjects` | Teaching subjects (Mathematics, Integrated Science, English...). |
| `subject_combinations` | **Fixed catalog** of NCCE-approved double-major pairings (major + optional minor). |
| `levels` | **Programme-type-aware** schemes: NCE {NCE1,NCE2,NCE3}, Degree {300,400}. Never collide. |
| `courses` | Department-owned, **programme-type-scoped** (NCE and Degree pools stay separate). |
| `curriculum_courses` | Per programme/level/semester course list — drives the Registration course form. |
| `course_prerequisites` | Course -> prerequisite course. |
| `course_allocations` | Lecturer workload per session/semester. |

## Completes the People link

Migration `..._add_academics_foreign_keys_to_enrolments` adds the real foreign keys to the
`programme_id`, `current_level_id`, and `subject_combination_id` columns that the People
module left as nullable placeholders. (Skipped on SQLite, which can't ALTER-add FKs; applied
on PostgreSQL.)

## The contract other modules use

```php
use Modules\Academics\Contracts\AcademicStructure;

$levels = $structure->levelsFor('NCE');                 // ordered NCE scheme
$form   = $structure->curriculumFor($programmeId, $levelId, $semester); // course-form source
```

Bind it in the module service provider's `register()`:

```php
$this->app->bind(
    \Modules\Academics\Contracts\AcademicStructure::class,
    \Modules\Academics\Services\AcademicStructureService::class,
);
```

## Optional: wire Enrolment relationships

Now that Academics exists, you may add these to `Modules/People/app/Models/Enrolment.php`
so an enrolment can resolve its programme/level/combination:

```php
public function programme(): BelongsTo
{
    return $this->belongsTo(\Modules\Academics\Models\Programme::class);
}

public function level(): BelongsTo
{
    return $this->belongsTo(\Modules\Academics\Models\Level::class, 'current_level_id');
}
```

## Invariants (tested)

- NCE and Degree level schemes stay separate; levels return ordered by rank.
- NCE and Degree course pools stay separate.
- Subject combinations are built from catalog subjects; minor is optional.
- `curriculumFor()` returns the right course list, narrowable by semester.

## Run

```bash
php artisan module:migrate Academics
php artisan module:seed Academics
php artisan test --filter='LevelScheme|CourseAndCombination|Curriculum'
```
