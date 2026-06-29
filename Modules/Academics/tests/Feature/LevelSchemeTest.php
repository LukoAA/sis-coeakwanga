<?php

use Modules\Academics\Models\Level;
use Modules\Academics\Models\Programme;
use Modules\Academics\Services\AcademicStructureService;

beforeEach(function () {
    $this->structure = new AcademicStructureService();

    Level::factory()->createMany([
        ['programme_type' => Programme::TYPE_NCE, 'code' => 'NCE1', 'label' => 'NCE 1', 'rank' => 1],
        ['programme_type' => Programme::TYPE_NCE, 'code' => 'NCE2', 'label' => 'NCE 2', 'rank' => 2],
        ['programme_type' => Programme::TYPE_NCE, 'code' => 'NCE3', 'label' => 'NCE 3', 'rank' => 3],
        ['programme_type' => Programme::TYPE_DEGREE, 'code' => '300', 'label' => '300 Level', 'rank' => 1],
        ['programme_type' => Programme::TYPE_DEGREE, 'code' => '400', 'label' => '400 Level', 'rank' => 2],
    ]);
});

it('keeps NCE and Degree level schemes separate (ADR-0001)', function () {
    $nce = $this->structure->levelsFor(Programme::TYPE_NCE);
    $degree = $this->structure->levelsFor(Programme::TYPE_DEGREE);

    expect($nce)->toHaveCount(3)
        ->and($degree)->toHaveCount(2)
        ->and($nce->pluck('code')->all())->toBe(['NCE1', 'NCE2', 'NCE3'])
        ->and($degree->pluck('code')->all())->toBe(['300', '400'])
        // no NCE code leaks into the degree scheme and vice versa
        ->and($nce->pluck('code')->intersect($degree->pluck('code')))->toBeEmpty();
});

it('returns levels ordered by rank', function () {
    $nce = $this->structure->levelsFor(Programme::TYPE_NCE);

    expect($nce->pluck('rank')->all())->toBe([1, 2, 3]);
});
