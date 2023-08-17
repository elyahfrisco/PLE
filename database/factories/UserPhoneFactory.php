<?php

namespace Database\Factories;

use App\Models\UserPhone;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPhoneFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = UserPhone::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'phone' => $this->faker->phoneNumber,
      'type' => Arr::random(['portable', 'fixe']),
      'owner' => null,
    ];
  }
}
