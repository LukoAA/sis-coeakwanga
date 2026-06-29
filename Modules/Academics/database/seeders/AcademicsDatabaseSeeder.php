<?php

namespace Modules\Academics\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Academics\Models\Course;
use Modules\Academics\Models\Department;
use Modules\Academics\Models\Level;
use Modules\Academics\Models\Programme;
use Modules\Academics\Models\School;
use Modules\Academics\Models\Subject;
use Modules\Academics\Models\SubjectCombination;

class AcademicsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- The level schemes (programme-type-aware, ADR-0001) ---
        $nceLevels = [
            ['programme_type' => Programme::TYPE_NCE, 'code' => 'NCE1', 'label' => 'NCE 1', 'rank' => 1],
            ['programme_type' => Programme::TYPE_NCE, 'code' => 'NCE2', 'label' => 'NCE 2', 'rank' => 2],
            ['programme_type' => Programme::TYPE_NCE, 'code' => 'NCE3', 'label' => 'NCE 3', 'rank' => 3],
        ];
        $degreeLevels = [
            ['programme_type' => Programme::TYPE_DEGREE, 'code' => '300', 'label' => '300 Level', 'rank' => 1],
            ['programme_type' => Programme::TYPE_DEGREE, 'code' => '400', 'label' => '400 Level', 'rank' => 2],
        ];
        foreach (array_merge($nceLevels, $degreeLevels) as $lvl) {
            Level::query()->updateOrCreate(
                ['programme_type' => $lvl['programme_type'], 'code' => $lvl['code']],
                $lvl,
            );
        }

        // --- Subjects (teaching subjects for NCE combinations) ---
        $subjects = collect([
            'Mathematics', 'Integrated Science', 'English', 'Social Studies',
        ])->mapWithKeys(fn ($name) => [
            $name => Subject::query()->updateOrCreate(
                ['code' => 'SUB-'.strtoupper(substr(str_replace(' ', '', $name), 0, 4))],
                ['name' => $name],
            ),
        ]);

        // --- School -> Departments -> NCE programmes ---
        $school = School::query()->updateOrCreate(
            ['code' => 'SCI-EDU'],
            ['name' => 'School of Science Education'],
        );

        $mathDept = Department::query()->updateOrCreate(
            ['code' => 'DPT-MTH'],
            ['school_id' => $school->id, 'name' => 'Department of Mathematics'],
        );
        $langDept = Department::query()->updateOrCreate(
            ['code' => 'DPT-LNG'],
            ['school_id' => $school->id, 'name' => 'Department of Languages'],
        );

        $nceMath = Programme::query()->updateOrCreate(
            ['code' => 'NCE-MTH-ISC'],
            [
                'department_id' => $mathDept->id,
                'name' => 'NCE Mathematics / Integrated Science',
                'programme_type' => Programme::TYPE_NCE,
                'award' => 'Nigerian Certificate in Education',
                'duration_years' => 3,
            ],
        );

        $nceEng = Programme::query()->updateOrCreate(
            ['code' => 'NCE-ENG-SOS'],
            [
                'department_id' => $langDept->id,
                'name' => 'NCE English / Social Studies',
                'programme_type' => Programme::TYPE_NCE,
                'award' => 'Nigerian Certificate in Education',
                'duration_years' => 3,
            ],
        );

        // --- Fixed subject-combination catalog ---
        SubjectCombination::query()->updateOrCreate(
            ['programme_id' => $nceMath->id, 'name' => 'Mathematics / Integrated Science'],
            [
                'major_subject_id' => $subjects['Mathematics']->id,
                'minor_subject_id' => $subjects['Integrated Science']->id,
            ],
        );
        SubjectCombination::query()->updateOrCreate(
            ['programme_id' => $nceEng->id, 'name' => 'English / Social Studies'],
            [
                'major_subject_id' => $subjects['English']->id,
                'minor_subject_id' => $subjects['Social Studies']->id,
            ],
        );

        // --- A couple of NCE courses (course pool is programme-type-scoped) ---
        Course::query()->updateOrCreate(
            ['code' => 'MTH111'],
            [
                'department_id' => $mathDept->id,
                'programme_type' => Programme::TYPE_NCE,
                'title' => 'Algebra I',
                'credit_units' => 2,
                'course_type' => Course::TYPE_CORE,
            ],
        );

        // --- A Degree programme (award/structure pending affiliating university [CONFIRM]) ---
        Programme::query()->updateOrCreate(
            ['code' => 'BED-MTH'],
            [
                'department_id' => $mathDept->id,
                'name' => 'B.Ed Mathematics (top-up)',
                'programme_type' => Programme::TYPE_DEGREE,
                'award' => null,
                'duration_years' => 2,
            ],
        );
    }
}
