<?php

namespace Modules\Examinations\Database\Seeders;
use Modules\Identity\Models\Setting;

use Illuminate\Database\Seeder;

class ExaminationsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
    }

    Setting::put('examinations.docket_prefix', 'DKT')
}
