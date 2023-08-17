<?php

namespace Database\Factories;

use App\Models\Planning;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanningFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Planning::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $time_start = $this->faker->time();
    $time_end = $this->faker->time();
    // return [
    //     'day' => [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ],
    //     'time_start' => $this->faker->,
    //     'time_end' => $this->faker->,
    // ];
  }
}
