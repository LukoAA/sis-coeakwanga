<?php

namespace Modules\Academics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Academics\Database\Factories\SchoolFactory;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    protected static function newFactory(): SchoolFactory
    {
        return SchoolFactory::new();
    }
}
