<?php

namespace Modules\Academics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Level;
use Modules\Academics\Models\Programme;

class LevelFactory extends Factory
{
    protected $model = Level::class;

    public function definition(): array
    {
        return [
            'programme_type' => Programme::TYPE_NCE,
            'code' => 'NCE1',
            'label' => 'NCE 1',
            'rank' => 1,
        ];
    }
}
