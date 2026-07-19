<?php

namespace Modules\Identity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Semester;
use Modules\Identity\Models\Setting;

class IdentityDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $session = AcademicSession::query()->updateOrCreate(
            ['name' => '2025/2026'],
            [
                'starts_on' => '2025-09-01',
                'ends_on' => '2026-07-31',
                'is_current' => true,
            ],
        );

        Semester::query()->updateOrCreate(
            ['academic_session_id' => $session->id, 'name' => 'First'],
            ['is_current' => true],
        );

        Semester::query()->updateOrCreate(
            ['academic_session_id' => $session->id, 'name' => 'Second'],
            ['is_current' => false],
        );

        // Configurable defaults — change these in settings, never in code.
        Setting::put('attendance_eligibility_threshold', 75); // percent required to sit exams
        Setting::put('add_drop_open', false);
        Setting::put('institution_name', 'College of Education, Akwanga');

        // Core staff roles (Identity owns RBAC).
        foreach (['registrar', 'bursar', 'lecturer', 'it_admin', 'student'] as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
