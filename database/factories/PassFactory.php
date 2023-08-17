<?php

namespace Database\Factories;

use App\Models\Pass;
use Illuminate\Database\Eloquent\Factories\Factory;

class PassFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Pass::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $passData = [
      'name' => $this->faker->words(rand(1, 2), true),
      'number_sessions' => rand(4, 6),
    ];

    if (rand(0, 1)) {
      $passData['period_validity'] = rand($passData['number_sessions'], $passData['number_sessions'] + 4);
      $passData['period_validity_unit'] = 'week';
    }

    return $passData;
  }
}
