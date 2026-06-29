<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Database\Factories\FeeStructureFactory;

class FeeStructure extends Model
{
    use HasFactory;

    public const TYPE_ACCEPTANCE = 'ACCEPTANCE';
    public const TYPE_TUITION = 'TUITION';
    public const TYPE_SUNDRY = 'SUNDRY';

    protected $fillable = [
        'name', 'fee_type', 'programme_type', 'academic_session_id',
        'programme_id', 'level_id', 'amount',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    protected static function newFactory(): FeeStructureFactory
    {
        return FeeStructureFactory::new();
    }
}
