<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Activity;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $durations = ['01:00', '01:30'];
        $activities = [];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'bébés nageurs',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
            'for_kid' => true,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'jardin aquatique',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
            'for_kid' => true,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'école de natation',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
            'for_kid' => true,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'aquagym',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'aquabike',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'aquajump',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'aqua stand up paddle',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'natation adulte',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'lutte contre l\'aquphobie',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'aquagym prénatale',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'stage aquaphobie',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'balnéo-kinés',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'DIMANCHE BIEN-ETRE',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'MEDYJET',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
            'care' => true,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'LAGON DE RELAXATION',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
            'care' => true,
        ];

        $color = Color::inRandomOrder()->first();
        $activities[] = [
            'name' => 'BAINS',
            'description' => '',
            'duration' => Arr::random($durations),
            'background_color' => $color->background,
            'font_color' => $color->font,
            'care' => true,
        ];

        foreach ($activities as $key => $activitie) {
            Activity::create($activitie);
        }
    }
}
