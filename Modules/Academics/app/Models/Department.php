<?php

namespace Modules\Academics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Academics\Database\Factories\DepartmentFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'name', 'code'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function programmes(): HasMany
    {
        return $this->hasMany(Programme::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    protected static function newFactory(): DepartmentFactory
    {
        return DepartmentFactory::new();
    }
}
