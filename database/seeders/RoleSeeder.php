<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $roles = [];

    $roles[] = [
      "name" => 'administrateur',
      "deletable" => false,
    ];

    $roles[] = [
      "name" => 'client',
      "deletable" => false,
    ];

    $roles[] = [
      "name" => 'coach',
      "deletable" => false,
    ];

    $roles[] = [
      "name" => 'assistant commerciale',
      "deletable" => false,
    ];

    $roles[] = [
      "name" => 'intervenant exterieur',
      "deletable" => false,
    ];

    foreach ($roles as $key => $role) {
      Role::create($role);
    }
  }
}
