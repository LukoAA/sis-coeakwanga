<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;
use Modules\People\Services\PeopleDirectoryService;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->directory = new PeopleDirectoryService();
});

it('ranks a matric-number hit above a name-only hit', function () {
    // The real returning graduate, holding a known NCE matric.
    $real = Person::factory()->create([
        'surname' => 'Abubakar',
        'first_name' => 'Musa',
        'date_of_birth' => '2001-03-14',
        'phone' => '08030000001',
    ]);
    Enrolment::factory()->nce()->for($real)->create(['matric_number' => 'NCE/2019/0421']);

    // A different person who merely shares the surname.
    Person::factory()->create([
        'surname' => 'Abubakar',
        'first_name' => 'Sani',
        'date_of_birth' => '1999-01-01',
        'phone' => '08099999999',
    ]);

    $matches = $this->directory->findPotentialMatches([
        'matric_number' => 'NCE/2019/0421',
        'surname' => 'Abubakar',
    ]);

    expect($matches->first()['person']->id)->toBe($real->id)
        ->and($matches->first()['score'])->toBeGreaterThanOrEqual(100);
});

it('surfaces a candidate from name + dob + phone without a matric number', function () {
    $person = Person::factory()->create([
        'surname' => 'Okeke',
        'first_name' => 'Ada',
        'date_of_birth' => '2002-05-20',
        'phone' => '08055555555',
    ]);

    $matches = $this->directory->findPotentialMatches([
        'surname' => 'Okeke',
        'first_name' => 'Ada',
        'date_of_birth' => '2002-05-20',
        'phone' => '08055555555',
    ]);

    expect($matches)->toHaveCount(1)
        ->and($matches->first()['person']->id)->toBe($person->id)
        ->and($matches->first()['reasons'])->toContain('phone match');
});

it('returns nothing when no one plausibly matches', function () {
    Person::factory()->create(['surname' => 'Bello', 'phone' => '08011111111']);

    $matches = $this->directory->findPotentialMatches([
        'surname' => 'Nwosu',
        'phone' => '08022222222',
    ]);

    expect($matches)->toBeEmpty();
});

it('creates a person and records a second enrolment against them', function () {
    $person = $this->directory->createPerson([
        'surname' => 'Yakubu',
        'first_name' => 'Grace',
        'gender' => 'female',
        'date_of_birth' => '2000-09-09',
        'phone' => '08066666666',
    ]);

    $this->directory->recordEnrolment($person, [
        'matric_number' => 'DEG/2025/0100',
        'programme_type' => Enrolment::TYPE_DEGREE,
        'entry_route' => Enrolment::ROUTE_DIRECT_ENTRY,
    ]);

    expect(Person::count())->toBe(1)
        ->and($person->enrolments()->count())->toBe(1)
        ->and($person->enrolments()->first()->matric_number)->toBe('DEG/2025/0100');
});
