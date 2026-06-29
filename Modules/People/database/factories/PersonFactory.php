<?php

namespace Modules\People\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\People\Models\Person;

class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        return [
            'surname' => $this->faker->lastName(),
            'first_name' => $this->faker->firstName(),
            'other_names' => $this->faker->optional()->firstName(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'date_of_birth' => $this->faker->dateTimeBetween('-30 years', '-16 years')->format('Y-m-d'),
            'phone' => $this->faker->unique()->numerify('080########'),
            'email' => $this->faker->optional()->safeEmail(),
            'state_of_origin' => $this->faker->optional()->state(),
            'lga' => null,
        ];
    }
}
