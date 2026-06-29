<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Finance\Database\Factories\InvoiceFactory;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;

class Invoice extends Model
{
    use HasFactory;

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PART = 'part';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'enrolment_id', 'application_id', 'academic_session_id', 'total', 'status', 'due_on',
    ];

    protected function casts(): array
    {
        return ['total' => 'decimal:2', 'due_on' => 'date'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(Enrolment::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function confirmedPaymentsTotal(): float
    {
        return (float) $this->payments()->where('status', Payment::STATUS_CONFIRMED)->sum('amount');
    }

    public function balance(): float
    {
        return round((float) $this->total - $this->confirmedPaymentsTotal(), 2);
    }

    public function percentPaid(): float
    {
        $total = (float) $this->total;

        return $total > 0 ? round($this->confirmedPaymentsTotal() / $total * 100, 2) : 0.0;
    }

    /** Recompute unpaid/part/paid from confirmed payments. */
    public function refreshStatus(): void
    {
        $paid = $this->confirmedPaymentsTotal();
        $total = (float) $this->total;

        $status = match (true) {
            $paid <= 0 => self::STATUS_UNPAID,
            $paid + 0.001 >= $total => self::STATUS_PAID,
            default => self::STATUS_PART,
        };

        $this->update(['status' => $status]);
    }

    protected static function newFactory(): InvoiceFactory
    {
        return InvoiceFactory::new();
    }
}
