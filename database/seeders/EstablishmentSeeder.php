<?php

namespace Database\Seeders;

use App\Models\Establishment;
use Illuminate\Database\Seeder;

class EstablishmentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    $establishments = [];

    $establishments[] = [
      'name' => 'BORDEAUX',
      'address' => '267, Avenue d’Arès',
      'email' => 'email@admin.com',
      'postal_code' => '33200',
      'phone' => '05 56 96 95 13',
      'start_time' => '09:30:00',
      'end_time' => '19:00:00',
      'latitude' => "44.8384768",
      'longitude' => "-0.6139835",
    ];

    $establishments[] = [
      'name' => 'EYSINES',
      'address' => '9, avenue de la Forêt',
      'email' => 'email@admin.com',
      'postal_code' => '33320',
      'phone' => '05 56 96 95 13',
      'start_time' => '09:30:00',
      'end_time' => '19:00:00',
      'latitude' => "44.866511",
      'longitude' => "-0.6566078",
    ];

    $establishments[] = [
      'name' => 'BAINS & SOINS',
      'address' => 'Eysines',
      'email' => 'email@admin.com',
      'postal_code' => '33320',
      'phone' => '05 56 96 95 13',
      'start_time' => '09:30:00',
      'end_time' => '19:00:00',
      'relaxation_center' => true,
    ];

    foreach ($establishments as $key => $establishment) {
      Establishment::create($establishment);
    }
  }
}
