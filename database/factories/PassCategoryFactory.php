<?php

namespace Database\Factories;

use App\Models\PassCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PassCategoryFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = PassCategory::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'name' => $this->faker->sentence(),
    ];
  }
}
