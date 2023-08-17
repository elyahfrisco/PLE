<?php

namespace Database\Factories;

use App\Models\Establishment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstablishmentFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Establishment::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'name' => $this->faker->city,
      'address' => $this->faker->address,
      'email' => $this->faker->safeEmail,
      'postal_code' => $this->faker->postcode,
      'phone' => $this->faker->phoneNumber,
      'start_time' => '09:00:00',
      'end_time' => '17:00:00',
      'latitude' => $this->faker->latitude,
      'longitude' => $this->faker->longitude,
    ];
  }
}
