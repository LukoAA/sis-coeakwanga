<?php

namespace Modules\Admissions\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Academics\Models\Programme;
use Modules\Admissions\Models\MatricNumberFormat;
use Modules\Identity\Models\Setting;

class AdmissionsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::put('institution_code', 'COEA');
        Setting::put('admissions.offer_mode.UTME', 'manual');
        Setting::put('admissions.offer_mode.DIRECT_ENTRY', 'manual');
        Setting::put('admissions.auto_offer_cutoff', 50);

        // NCE: full six-part format (with subject combination).
        MatricNumberFormat::query()->updateOrCreate(
            ['programme_type' => Programme::TYPE_NCE, 'academic_session_id' => null],
            ['pattern' => '{institution}/{year}/{school}/{major}/{minor}/{serial}', 'serial_length' => 4],
        );

        // Degree: no minor subject — drop that token.
        MatricNumberFormat::query()->updateOrCreate(
            ['programme_type' => Programme::TYPE_DEGREE, 'academic_session_id' => null],
            ['pattern' => '{institution}/{year}/{school}/{major}/{serial}', 'serial_length' => 4],
        );
    }
}
