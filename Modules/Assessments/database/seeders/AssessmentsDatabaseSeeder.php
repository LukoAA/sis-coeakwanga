<?php

namespace Modules\Assessments\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Academics\Models\Programme;
use Modules\Assessments\Models\Classification;
use Modules\Assessments\Models\GradeBand;
use Modules\Assessments\Models\GradingScale;
use Modules\Identity\Models\Setting;

class AssessmentsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // CA/exam split and pass mark — all configurable.
        Setting::put('assessments.ca_max', 30);
        Setting::put('assessments.exam_max', 70);
        Setting::put('assessments.pass_mark', 40);

        // Representative 5-point band set, shared shape for both programme types.
        // NOTE: exact NCCE / affiliating-university values should be confirmed;
        // these are configurable defaults, not hard-coded rules.
        $bands = [
            ['min' => 70, 'max' => 100, 'letter' => 'A', 'point' => 5.00, 'pass' => true],
            ['min' => 60, 'max' => 69.99, 'letter' => 'B', 'point' => 4.00, 'pass' => true],
            ['min' => 50, 'max' => 59.99, 'letter' => 'C', 'point' => 3.00, 'pass' => true],
            ['min' => 45, 'max' => 49.99, 'letter' => 'D', 'point' => 2.00, 'pass' => true],
            ['min' => 40, 'max' => 44.99, 'letter' => 'E', 'point' => 1.00, 'pass' => true],
            ['min' => 0, 'max' => 39.99, 'letter' => 'F', 'point' => 0.00, 'pass' => false],
        ];

        foreach ([Programme::TYPE_NCE, Programme::TYPE_DEGREE] as $type) {
            $scale = GradingScale::updateOrCreate(
                ['programme_type' => $type],
                ['name' => $type.' 5-point scale'],
            );

            foreach ($bands as $b) {
                GradeBand::updateOrCreate(
                    ['grading_scale_id' => $scale->id, 'grade_letter' => $b['letter']],
                    ['min_score' => $b['min'], 'max_score' => $b['max'], 'grade_point' => $b['point'], 'is_pass' => $b['pass']],
                );
            }
        }

        // Degree classifications (placeholder — pending affiliating university [CONFIRM]).
        $degreeClasses = [
            ['min' => 4.50, 'max' => 5.00, 'label' => 'First Class'],
            ['min' => 3.50, 'max' => 4.49, 'label' => 'Second Class Upper'],
            ['min' => 2.40, 'max' => 3.49, 'label' => 'Second Class Lower'],
            ['min' => 1.50, 'max' => 2.39, 'label' => 'Third Class'],
            ['min' => 1.00, 'max' => 1.49, 'label' => 'Pass'],
        ];
        // NCE classifications (representative NCCE labels).
        $nceClasses = [
            ['min' => 4.50, 'max' => 5.00, 'label' => 'Distinction'],
            ['min' => 3.50, 'max' => 4.49, 'label' => 'Upper Credit'],
            ['min' => 2.40, 'max' => 3.49, 'label' => 'Lower Credit'],
            ['min' => 1.50, 'max' => 2.39, 'label' => 'Merit'],
            ['min' => 1.00, 'max' => 1.49, 'label' => 'Pass'],
        ];

        foreach ($degreeClasses as $c) {
            Classification::updateOrCreate(
                ['programme_type' => Programme::TYPE_DEGREE, 'label' => $c['label']],
                ['min_cgpa' => $c['min'], 'max_cgpa' => $c['max']],
            );
        }
        foreach ($nceClasses as $c) {
            Classification::updateOrCreate(
                ['programme_type' => Programme::TYPE_NCE, 'label' => $c['label']],
                ['min_cgpa' => $c['min'], 'max_cgpa' => $c['max']],
            );
        }
    }
}
