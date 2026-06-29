<?php

namespace Modules\Academics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Academics\Database\Factories\SubjectFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    protected static function newFactory(): SubjectFactory
    {
        return SubjectFactory::new();
    }
}
