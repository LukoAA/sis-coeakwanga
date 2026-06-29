<?php

namespace Modules\Finance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Programme;
use Modules\Finance\Models\FeeStructure;
use Modules\Identity\Models\AcademicSession;

class FeeStructureFactory extends Factory
{
    protected $model = FeeStructure::class;

    public function definition(): array
    {
        return [
            'name' => 'Tuition',
            'fee_type' => FeeStructure::TYPE_TUITION,
            'programme_type' => Programme::TYPE_NCE,
            'academic_session_id' => AcademicSession::factory(),
            'programme_id' => null,
            'level_id' => null,
            'amount' => 50000,
        ];
    }

    public function acceptance(float $amount = 10000): static
    {
        return $this->state(fn () => [
            'name' => 'Acceptance Fee',
            'fee_type' => FeeStructure::TYPE_ACCEPTANCE,
            'amount' => $amount,
        ]);
    }

    public function sundry(string $name, float $amount): static
    {
        return $this->state(fn () => [
            'name' => $name,
            'fee_type' => FeeStructure::TYPE_SUNDRY,
            'amount' => $amount,
        ]);
    }
}
