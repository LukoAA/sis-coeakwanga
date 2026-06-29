# Admissions

Turns applicants into enrolled students. Where People's matcher, Academics'
programmes/combinations, and Identity's session all converge.

## Flow

```
submitApplication
  -> findReturningPersonMatches   (Direct Entry: surface existing person, officer confirms)
  -> screen (+ optional auto-offer)
  -> makeOffer / acceptOffer
  -> finaliseAdmission            (GATED on acceptance-fee payment)
       -> generate matric number  (configurable COEA/.../serial format)
       -> create Enrolment against the existing or new Person
```

## Decisions baked in

- **Matric gated on payment.** `finaliseAdmission` checks `AcceptanceFeeGate` and throws
  `AcceptanceFeeUnpaidException` if unpaid. The interim gate reads a manual flag; bind a
  Finance-backed gate later without touching Admissions.
- **Configurable matric format**, e.g. `COEA/2022/SC/CSC/ECO/0233` =
  `{institution}/{year}/{school}/{major}/{minor}/{serial}`. Degree drops `{minor}`.
  Serial is allocated atomically per programme + session.
- **Manual offers by default**, with a per-intake `admissions.offer_mode.{ROUTE}` setting
  that can be flipped to `auto` (offer when score >= `admissions.auto_offer_cutoff`).

## Tables

`applications`, `offers`, `matric_number_formats`, `matric_serials`, `jamb_imports`.

## Bind the fee gate

In the module service provider's `register()`:

```php
$this->app->bind(
    \Modules\Admissions\Contracts\AcceptanceFeeGate::class,
    \Modules\Admissions\Services\InterimAcceptanceFeeGate::class,
);
```

## Invariants (tested)

- Matric generation is blocked until the acceptance fee is paid.
- A returning NCE graduate is admitted as a SECOND enrolment under the same person.
- The generator produces the exact configured format; serial increments per programme/session.
- Offers are manual by default; auto-offer only when configured and the cutoff is cleared.

## Run

```bash
php artisan module:migrate Admissions
php artisan module:seed Admissions
php artisan test Modules/Admissions/tests
```
