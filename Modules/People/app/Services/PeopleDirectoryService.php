<?php

namespace Modules\People\Services;

use Illuminate\Support\Collection;
use Modules\People\Contracts\PeopleDirectory;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;

class PeopleDirectoryService implements PeopleDirectory
{
    /**
     * Score weights. A matric number is system-issued and near-unique, so it
     * dominates. The rest are soft signals that accumulate.
     */
    private const SCORE_MATRIC = 100;
    private const SCORE_PHONE = 40;
    private const SCORE_DOB = 30;
    private const SCORE_SURNAME = 20;
    private const SCORE_FIRST_NAME = 10;

    /** Candidates scoring below this are not surfaced. */
    private const THRESHOLD = 30;

    public function findPotentialMatches(array $criteria): Collection
    {
        /** @var array<int, array{person:Person, score:int, reasons:array<int,string>}> $scored */
        $scored = [];

        // 1) Strongest signal: a matric number they can supply resolves directly
        //    to the person who holds that enrolment.
        if (! empty($criteria['matric_number'])) {
            $enrolment = Enrolment::query()
                ->where('matric_number', $criteria['matric_number'])
                ->first();

            if ($enrolment) {
                $scored[$enrolment->person_id] = [
                    'person' => $enrolment->person,
                    'score' => self::SCORE_MATRIC,
                    'reasons' => ['matric number match'],
                ];
            }
        }

        // 2) Soft signals: phone, DOB, and name. Query a candidate pool, then score.
        $candidates = Person::query()
            ->when(! empty($criteria['phone']), fn ($q) => $q->orWhere('phone', $criteria['phone']))
            ->when(! empty($criteria['surname']), fn ($q) => $q->orWhereRaw('lower(surname) = ?', [mb_strtolower($criteria['surname'])]))
            ->when(! empty($criteria['date_of_birth']), fn ($q) => $q->orWhere('date_of_birth', $criteria['date_of_birth']))
            ->limit(50)
            ->get();

        foreach ($candidates as $person) {
            $score = 0;
            $reasons = [];

            if (! empty($criteria['phone']) && $person->phone === $criteria['phone']) {
                $score += self::SCORE_PHONE;
                $reasons[] = 'phone match';
            }

            if (! empty($criteria['date_of_birth'])
                && $person->date_of_birth?->toDateString() === $criteria['date_of_birth']) {
                $score += self::SCORE_DOB;
                $reasons[] = 'date of birth match';
            }

            if (! empty($criteria['surname'])
                && mb_strtolower($person->surname) === mb_strtolower($criteria['surname'])) {
                $score += self::SCORE_SURNAME;
                $reasons[] = 'surname match';
            }

            if (! empty($criteria['first_name'])
                && mb_strtolower($person->first_name) === mb_strtolower($criteria['first_name'])) {
                $score += self::SCORE_FIRST_NAME;
                $reasons[] = 'first name match';
            }

            if ($score < self::THRESHOLD) {
                continue;
            }

            // Merge with any matric hit for the same person.
            if (isset($scored[$person->id])) {
                $scored[$person->id]['score'] += $score;
                $scored[$person->id]['reasons'] = array_merge($scored[$person->id]['reasons'], $reasons);
            } else {
                $scored[$person->id] = [
                    'person' => $person,
                    'score' => $score,
                    'reasons' => $reasons,
                ];
            }
        }

        return collect(array_values($scored))->sortByDesc('score')->values();
    }

    public function createPerson(array $attributes): Person
    {
        return Person::create($attributes);
    }

    public function recordEnrolment(Person $person, array $attributes): Enrolment
    {
        return $person->enrolments()->create($attributes);
    }
}
