<?php

namespace Database\Seeders;

use App\Models\Establishment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ActivitySessionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Establishment::all()->each(function ($establishment) {
      try {
        Http::timeout(0.001)->get(route('establishments.plannings.sessions.organize.all', $establishment->id));
      } catch (\Throwable $th) {
      }
    });
  }
}
