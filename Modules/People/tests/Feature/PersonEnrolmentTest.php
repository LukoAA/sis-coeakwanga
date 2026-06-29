<?php

use Illuminate\Database\QueryException;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;

it('lets a person hold many enrolments', function () {
    $person = Person::factory()->create();

    Enrolment::factory()->nce()->for($person)->create();
    Enrolment::factory()->degree()->for($person)->create();

    expect($person->enrolments()->count())->toBe(2);
});

it('recognises a returning NCE graduate as the same person with a second enrolment', function () {
    // First admission: NCE, later graduated.
    $person = Person::factory()->create();
    $nce = Enrolment::factory()->nce()->graduated()->for($person)->create();

    // Years later: re-admitted to the Degree at Direct Entry — SAME person.
    $degree = Enrolment::factory()->degree()->for($person)->create();

    expect(Person::count())->toBe(1)                       // not duplicated
        ->and($person->enrolments()->count())->toBe(2)
        ->and($nce->programme_type)->toBe(Enrolment::TYPE_NCE)
        ->and($nce->status)->toBe(Enrolment::STATUS_GRADUATED)
        ->and($degree->programme_type)->toBe(Enrolment::TYPE_DEGREE)
        ->and($degree->entry_route)->toBe(Enrolment::ROUTE_DIRECT_ENTRY)
        ->and($degree->person_id)->toBe($person->id);
});

it('forbids two enrolments sharing a matric number', function () {
    Enrolment::factory()->create(['matric_number' => 'NCE/2025/0001']);

    Enrolment::factory()->create(['matric_number' => 'NCE/2025/0001']);
})->throws(QueryException::class);

it('keeps the matric number on the enrolment, not the person', function () {
    $person = Person::factory()->create();
    $enrolment = Enrolment::factory()->nce()->for($person)->create();

    expect($person->getAttributes())->not->toHaveKey('matric_number')
        ->and($enrolment->matric_number)->not->toBeNull();
});
