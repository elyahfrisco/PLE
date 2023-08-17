<?php

namespace Database\Factories;

use App\Models\ActivitySubscriptionCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivitySubscriptionConditionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivitySubscriptionCondition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'content' => $this->faker->sentence(rand(10, 20)),
        ];
    }
}
