<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserComment;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserCommentFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = UserComment::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'content' => $this->faker->sentences(4, true),
      'commentator_id' => User::inRandomOrder()->select('id')->first()->id,
    ];
  }
}
