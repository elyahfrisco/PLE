<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Seeder;

class PostCategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $post_categories = [];

    $post_categories[] = [
      "name" => 'Actualité',
      "color" => '#EC7523',
    ];

    $post_categories[] = [
      "name" => 'Événement',
      "color" => '#E54347',
    ];

    $post_categories[] = [
      "name" => 'Promotion',
      "color" => '#0AA9DB',
    ];

    foreach ($post_categories as $key => $post_category) {
      PostCategory::firstOrCreate($post_category);
    }
  }
}
