<?php

namespace Modules\People\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;

class PeopleDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // A handful of straightforward NCE students.
        Person::factory()
            ->count(5)
            ->has(Enrolment::factory()->nce(), 'enrolments')
            ->create();

        // The headline case (ADR-0001): one human, two admissions over time.
        // Admitted to NCE, graduated, then re-admitted to the Degree at Direct Entry.
        $returning = Person::factory()->create([
            'surname' => 'Abubakar',
            'first_name' => 'Musa',
            'gender' => 'male',
            'date_of_birth' => '2001-03-14',
            'phone' => '08030000001',
        ]);

        $returning->enrolments()->createMany([
            [
                'matric_number' => 'NCE/2019/0421',
                'programme_type' => Enrolment::TYPE_NCE,
                'entry_route' => Enrolment::ROUTE_UTME,
                'status' => Enrolment::STATUS_GRADUATED,
                'graduation_outcome' => 'Upper Credit',
                'graduated_at' => '2022-07-31',
            ],
            [
                'matric_number' => 'DEG/2025/0007',
                'programme_type' => Enrolment::TYPE_DEGREE,
                'entry_route' => Enrolment::ROUTE_DIRECT_ENTRY,
                'status' => Enrolment::STATUS_ACTIVE,
            ],
        ]);
    }
}
