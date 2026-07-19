<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\People\Models\Enrolment;

class StudentAccountSeeder extends Seeder
{
    public function run(): void
    {
        $enrolments = Enrolment::with('person')
            ->where('status', Enrolment::STATUS_ACTIVE)
            ->get();

        foreach ($enrolments as $enrolment) {
            $person = $enrolment->person;

            if (! $person || User::where('person_id', $person->id)->exists()) {
                continue;
            }

            $user = User::create([
                'name' => $person->fullName(),
                // Derived login email from the matric (students often lack email).
                'email' => strtolower(str_replace('/', '.', $enrolment->matric_number)).'@student.coeakwanga.test',
                'password' => Hash::make('password'),   // demo default; force-change later
                'person_id' => $person->id,
            ]);

            $user->assignRole('student');
        }

        $this->command?->info('Student accounts created for active enrolments.');
    }
}