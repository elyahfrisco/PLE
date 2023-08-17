<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Post::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'title' => $this->faker->words(rand(7, 10), true),
      'content' => $this->faker->paragraphs(rand(2, 10), true),
      'user_id' => 1,
      'post_category_id' => PostCategory::inRandomOrder()->first()->id,
    ];
  }
}
