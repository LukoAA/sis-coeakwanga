<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Modules\Identity\Database\Seeders\IdentityDatabaseSeeder;
use Modules\People\Database\Seeders\PeopleDatabaseSeeder;
use Modules\Academics\Database\Seeders\AcademicsDatabaseSeeder;
use Modules\Admissions\Database\Seeders\AdmissionsDatabaseSeeder;
use Modules\Finance\Database\Seeders\FinanceDatabaseSeeder;
use Modules\Assessments\Database\Seeders\AssessmentsDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'registrar']);
        Role::firstOrCreate(['name' => 'bursar']);
        Role::firstOrCreate(['name' => 'lecturer']);
        Role::firstOrCreate(['name' => 'it_admin']);

        $superuser = User::factory()->create([
            'name' => 'IT Admin', 'email' => 'it@example.com', 'password' => 'password',
        ]);
        $superuser->assignRole('it_admin');

        $registrar = User::factory()->create([
            'name' => 'Registrar', 'email' => 'registrar@example.com', 'password' => 'password',
        ]);
        $registrar->assignRole('registrar');

        $bursar = User::factory()->create([
            'name' => 'Bursar', 'email' => 'bursar@example.com', 'password' => 'password',
        ]);
        $bursar->assignRole('bursar');

        $lecturer = User::factory()->create([
            'name' => 'Lecturer', 'email' => 'lecturer@example.com', 'password' => 'password',
        ]);
        $lecturer->assignRole('lecturer');

        $this->call([
            IdentityDatabaseSeeder::class,
            PeopleDatabaseSeeder::class,
            AcademicsDatabaseSeeder::class,
            AdmissionsDatabaseSeeder::class,
            FinanceDatabaseSeeder::class,
            AssessmentsDatabaseSeeder::class,
        ]);
    }
}