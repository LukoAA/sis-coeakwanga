<?php

namespace Modules\People\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\People\Database\Factories\PersonFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * The master human record. Created once, never duplicated. A returning NCE
 * graduate keeps THIS record and gains a second enrolment (see ADR-0001).
 */
class Person extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'surname',
        'first_name',
        'other_names',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'state_of_origin',
        'lga',
        'next_of_kin_name',
        'next_of_kin_phone',
        'next_of_kin_relationship',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    public function fullName(): string
    {
        return trim("{$this->surname} {$this->first_name} {$this->other_names}");
    }

    protected static function newFactory(): PersonFactory
    {
        return PersonFactory::new();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->singleFile()                       // passport photo — one, replaced on re-upload
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);

        $this->addMediaCollection('documents');  // O-level results, birth cert, etc. — many
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('avatar')      // small, for table lists
            ->fit(\Spatie\Image\Enums\Fit::Crop, 100, 100)
            ->nonQueued();

        $this->addMediaConversion('id_card')     // larger, for ID cards / dockets
            ->fit(\Spatie\Image\Enums\Fit::Crop, 400, 400)
            ->nonQueued();
    }
}
