<?php

namespace Database\Seeders;

use App\Models\ContactOrigin;
use Illuminate\Database\Seeder;

class ContactOriginSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $contact_origins = [
      'déjà venu aux',
      'je suis passé devant',
      'site internet',
      'professionnels de santé',
      'réseaux sociaux',
      'bouche à oreille',
      'autre',
    ];
    foreach ($contact_origins as $key => $contact_origin) {
      ContactOrigin::create(['designation' => $contact_origin]);
    }
  }
}
