<?php

namespace Database\Factories;

use Illuminate\Support\Arr;
use App\Models\ActivitySubscriptionQuestionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivitySubscriptionQuestionnaireFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = ActivitySubscriptionQuestionnaire::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'type' => Arr::random(['single', 'multiple']),
      'content' => $this->faker->sentence() . '?',
      'other_response' => rand(0, 1),
    ];
  }
}
