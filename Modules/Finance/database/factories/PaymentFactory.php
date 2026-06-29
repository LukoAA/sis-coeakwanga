<?php

namespace Modules\Finance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\Payment;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'gateway' => 'manual',
            'reference' => 'MAN-'.$this->faker->unique()->bothify('??########'),
            'amount' => 10000,
            'status' => Payment::STATUS_PENDING,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => Payment::STATUS_CONFIRMED, 'paid_at' => now()]);
    }
}
