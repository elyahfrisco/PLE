<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    $colors = [];
    $colors[] = ["background" => '#0C5D89'];
    // $colors[] = ["background" => '#FFFFFF', "font" => '#0c5d89'];
    $colors[] = ["background" => '#1F497D'];
    $colors[] = ["background" => '#00AAD4'];
    // $colors[] = ["background" => '#FFFFFF', "font" => '#9d022f'];
    $colors[] = ["background" => '#5c8727'];
    $colors[] = ["background" => '#d68019'];
    $colors[] = ["background" => '#D37D98'];
    $colors[] = ["background" => '#9d022f'];
    // $colors[] = ["background" => '#E8C005'];

    foreach ($colors as $key => $color) {
      Color::create($color);
    }
  }
}
