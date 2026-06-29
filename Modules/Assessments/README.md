# Assessments

The academic heart: the configurable grading engine, GPA/CGPA computation, the
result-approval workflow, and classifications. Closes out Phase 1.

## Tables

| Table | Purpose |
|---|---|
| `grading_scales` / `grade_bands` | Configurable score -> grade/point mapping, per programme type. |
| `classifications` | Configurable CGPA -> class (NCE: Distinction..Pass; Degree: First..Pass). |
| `score_entries` | CA + exam per student per course; computed total/grade/point/passed; lifecycle status. |
| `result_approvals` | Audit of each approval-stage transition. |
| `result_summaries` | Computed GPA/CGPA per session+semester, with classification. |

## Nothing is hard-coded

Grade thresholds, grade points, pass mark, CA/exam split, and classification bands all live
in tables/settings. The engine reads them — change regulations without touching code.

> The seeded NCE bands and Degree classifications are **representative defaults**. Confirm
> exact NCCE Minimum Standards values and the affiliating university's degree classes, then
> adjust the seed data. No code changes needed.

## Flow

```
enterScore (CA+exam -> total -> grade/point/passed via GradingEngine)
  -> submit -> vet -> approve -> publish     (ResultWorkflow, each step audited)
GpaCalculator.computeSemester -> result_summary (GPA, cumulative CGPA, classification)
```

GPA = sum(grade_point x credit_units) / sum(credit_units), over PUBLISHED entries only.

## The payoff: real carry-overs in Registration

`AssessmentsCarryOverProvider` implements Registration's `CarryOverProvider`. A published
failing result becomes a carry-over. In `RegistrationServiceProvider`, swap the binding:

```php
// was InterimCarryOverProvider
$this->app->bind(
    \Modules\Registration\Contracts\CarryOverProvider::class,
    \Modules\Assessments\Services\AssessmentsCarryOverProvider::class,
);
```

Course forms now auto-include failed courses — with no Registration code changes.

## Invariants (tested)

- Scores map to the configured grade band; sub-pass scores are flagged failing.
- GPA is credit-unit weighted; only published results count; classification follows CGPA.
- A published failed course is a carry-over; an unpublished one is not.

## Run

```bash
php artisan module:migrate Assessments
php artisan module:seed Assessments
php artisan test Modules/Assessments/tests
```
