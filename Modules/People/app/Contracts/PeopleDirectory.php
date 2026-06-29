<?php

namespace Modules\People\Contracts;

use Illuminate\Support\Collection;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;

/**
 * The public contract for the People module. Admissions and other modules
 * depend on THIS interface (resolved from the container) rather than reaching
 * into People's models or services directly.
 */
interface PeopleDirectory
{
    /**
     * Find existing people who may be the same human as an incoming applicant,
     * ranked strongest-first. Used to recognise a returning NCE graduate
     * instead of creating a duplicate. The caller (a human admissions officer)
     * confirms or rejects each suggestion — this never auto-merges.
     *
     * @param  array{matric_number?:string,surname?:string,first_name?:string,date_of_birth?:string,phone?:string}  $criteria
     * @return Collection<int, array{person:Person, score:int, reasons:array<int,string>}>
     */
    public function findPotentialMatches(array $criteria): Collection;

    /** Create a new master Person record. */
    public function createPerson(array $attributes): Person;

    /**
     * Record a new enrolment against an existing person — the returning-graduate
     * path. The person is preserved; a second enrolment is linked to them.
     */
    public function recordEnrolment(Person $person, array $attributes): Enrolment;
}
