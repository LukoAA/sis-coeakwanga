<?php

namespace Modules\Registration\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Identity\Models\Setting;

class RegistrationDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::put('registration.approval_mode', 'manual'); // 'manual' | 'auto'
        Setting::put('registration.add_drop_open', true);
        Setting::put('registration.min_units', 0);
        Setting::put('registration.max_units', 24);
    }
}
