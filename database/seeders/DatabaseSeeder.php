<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Academics\Database\Seeders\AcademicsDatabaseSeeder;
use Modules\Admissions\Database\Seeders\AdmissionsDatabaseSeeder;
use Modules\Assessments\Database\Seeders\AssessmentsDatabaseSeeder;
use Modules\Finance\Database\Seeders\FinanceDatabaseSeeder;
use Modules\Identity\Database\Seeders\IdentityDatabaseSeeder;
use Modules\People\Database\Seeders\PeopleDatabaseSeeder;
use Modules\Registration\Database\Seeders\RegistrationDatabaseSeeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1) Roles (idempotent). 'student' included — the portal gate needs it.
        foreach (['registrar', 'bursar', 'lecturer', 'it_admin', 'student'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // 2) Base data FIRST — Identity holds the current session that People,
        //    Academics, etc. depend on. Order matters.
        $this->call([
            IdentityDatabaseSeeder::class,
            PeopleDatabaseSeeder::class,
            AcademicsDatabaseSeeder::class,
            AdmissionsDatabaseSeeder::class,
            FinanceDatabaseSeeder::class,
            RegistrationDatabaseSeeder::class,
            AssessmentsDatabaseSeeder::class,
        ]);

        // 3) Staff logins — known passwords, correct roles. firstOrCreate so a
        //    re-run never duplicates or errors.
        $staff = [
            ['IT Admin',  'it@example.com',        'it_admin'],
            ['Registrar', 'registrar@example.com', 'registrar'],
            ['Bursar',    'bursar@example.com',    'bursar'],
            ['Lecturer',  'lecturer@example.com',  'lecturer'],
        ];

        foreach ($staff as [$name, $email, $role]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make('password')],
            );
            $user->assignRole($role);
        }

        // 4) Demo cohort, curriculum, and student logins — via real services.
        $this->call([
            DemoSeeder::class,
            CurriculumSeeder::class,
            StudentAccountSeeder::class,
        ]);
    }
}