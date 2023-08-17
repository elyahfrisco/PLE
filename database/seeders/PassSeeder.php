<?php

namespace Database\Seeders;

use App\Models\Pass;
use Illuminate\Database\Seeder;

class PassSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $passes = [];

    $passes[] = [
      "name" => 'PASS TRIMESTRIEL',
      "pass_trimester" => true,
      "number_sessions" => '15',
    ];

    $passes[] = [
      "name" => 'PASS DECOUVERTE',
      "number_sessions" => '10',
    ];

    $passes[] = [
      "name" => 'COUP PAR COUP',
      "number_sessions" => '1',
      "one_session" => true
    ];

    $passes[] = [
      "name" => 'ESSAI',
      "number_sessions" => '1',
      "one_session" => true
    ];

    $passes[] = [
      "name" => 'PASS BIEN ETRE',
    ];

    $passes[] = [
      "name" => 'PASS DETENTE',
    ];

    $passes[] = [
      "name" => 'PASS SPORT',
      "number_sessions" => '3',
    ];

    $passes[] = [
      "name" => '1 MEDYJET',
      "number_sessions" => '1',
      "care" => true,
    ];

    $passes[] = [
      "name" => '1 LAGON',
      "number_sessions" => '1',
      "care" => true,
    ];

    $passes[] = [
      "name" => '10 SOINS HYDROTHERAPIE',
      "number_sessions" => '10',
      "care" => true,
    ];

    $passes[] = [
      "name" => '1 BAIN',
      "number_sessions" => '1',
      "care" => true,
    ];

    $passes[] = [
      "name" => '10 BAINS',
      "number_sessions" => '10',
      "care" => true,
    ];

    $passes[] = [
      "name" => 'BAINS + 1 SOINS',
      "number_sessions" => '2',
      "care" => true,
    ];

    $passes[] = [
      "name" => 'BAINS + 2 SOINS',
      "number_sessions" => '3',
      "care" => true,
    ];

    $passes[] = [
      "name" => 'DBE + 1 SOIN',
      "number_sessions" => '2',
      "care" => true,
    ];

    foreach ($passes as $key => $pass) {
      Pass::create($pass);
    }
  }
}
