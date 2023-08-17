<?php

namespace Database\Factories;

use App\Models\Color;
use App\Models\Activity;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

function color_inverse($color)
{
  $color = str_replace('#', '', $color);
  if (strlen($color) != 6) {
    return '000000';
  }
  $rgb = '';
  for ($x = 0; $x < 3; $x++) {
    $c = 255 - hexdec(substr($color, (2 * $x), 2));
    $c = ($c < 0) ? 0 : dechex($c);
    $rgb .= (strlen($c) < 2) ? '0' . $c : $c;
  }
  return '#' . $rgb;
}

class ActivityFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Activity::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $color = Color::inRandomOrder()->first();
    return [
      'name' => $this->faker->words(rand(1, 2), true),
      'description' => $this->faker->paragraphs(rand(2, 3), true),
      'duration' => Arr::random(['00:30', '01:00', '01:30']),
      'background_color' => $color->background,
      'font_color' => $color->font,
    ];
  }
}
