<?php

namespace Modules\Academics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Academics\Database\Factories\LevelFactory;

/**
 * A study level WITHIN a programme-type scheme. NCE and Degree levels never
 * collide because every level carries its programme_type (ADR-0001).
 */
class Level extends Model
{
    use HasFactory;

    protected $fillable = ['programme_type', 'code', 'label', 'rank'];

    protected function casts(): array
    {
        return ['rank' => 'integer'];
    }

    public function scopeForType(Builder $query, string $programmeType): Builder
    {
        return $query->where('programme_type', $programmeType)->orderBy('rank');
    }

    protected static function newFactory(): LevelFactory
    {
        return LevelFactory::new();
    }
}
