# Finance

Fees, invoices, payments, and the fee-clearance gate. The first module that
handles money — every balance change and payment write is transactional and tested.

## Tables

| Table | Purpose |
|---|---|
| `fee_structures` | Configurable fees (acceptance/tuition/sundry) per session + programme type, optionally per programme/level. |
| `invoices` | One per enrolment+session (session fees) OR per application (acceptance fee). Itemized. |
| `invoice_items` | Line items tracing back to a fee structure. |
| `payments` | Pending/confirmed/failed payments against an invoice, with a gateway + reference. |

## Decisions baked in

- **Configurable percentage clearance.** `finance.clearance.register_threshold` (default 50)
  and `finance.clearance.exams_threshold` (default 100). Part-pay to register, full to sit exams.
- **Itemized invoices**, auto-generatable per enrolment or batch by the bursar.
- **Swappable gateway.** `PaymentGateway` interface with a `ManualPaymentGateway` stub today;
  a real Remita/Paystack/Flutterwave adapter binds in later with no service changes.

## Contracts other modules use

```php
use Modules\Finance\Contracts\FeeClearance;

$clearance->isClearedToRegister($enrolmentId, $sessionId); // Registration gate
$clearance->isClearedForExams($enrolmentId, $sessionId);   // Examinations gate
```

Bind these in the module service provider's `register()`:

```php
$this->app->bind(\Modules\Finance\Contracts\PaymentGateway::class, \Modules\Finance\Services\ManualPaymentGateway::class);
$this->app->bind(\Modules\Finance\Contracts\FeeClearance::class, \Modules\Finance\Services\FeeClearanceService::class);
```

## Replacing the Admissions interim gate (the payoff)

In `Modules/Admissions/app/Providers/AdmissionsServiceProvider.php`, swap the binding:

```php
// was: InterimAcceptanceFeeGate
$this->app->bind(
    \Modules\Admissions\Contracts\AcceptanceFeeGate::class,
    \Modules\Finance\Services\FinanceAcceptanceFeeGate::class,
);
```

Admissions now gates matric generation on a real, paid acceptance invoice — and no
Admissions code changed.

## Invariants (tested)

- Session invoices itemize from fee structures; acceptance fees never appear on them.
- Only confirmed payments reduce the balance; status moves unpaid -> part -> paid.
- 50% clears registration but not exams; 100% clears exams.
- The acceptance-fee gate opens only when the acceptance invoice is fully paid.

## Run

```bash
php artisan module:migrate Finance
php artisan module:seed Finance
php artisan test Modules/Finance/tests
```
