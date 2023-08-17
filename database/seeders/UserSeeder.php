<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    $faker = Factory::create();

    $users = [];
    $users[] = [
      'name' => 'Admin',
      'email' => 'admin@admin.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'admin',
      "birth_date" => 'admin',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 1,
    ];
    $users[] = [
      'name' => 'Admin1',
      'email' => 'admin1@admin.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'admin1',
      "birth_date" => 'admin1',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 1,
    ];
    $users[] = [
      'name' => 'Admin2',
      'email' => 'admin2@admin.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'admin2',
      "birth_date" => 'admin2',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 1,
    ];
    $users[] = [
      'name' => 'client',
      'email' => 'client@client.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'client',
      "birth_date" => 'client',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 2,
    ];
    $users[] = [
      'name' => 'client1',
      'email' => 'client1@client.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'client1',
      "birth_date" => 'client1',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 2,
    ];
    $users[] = [
      'name' => 'client2',
      'email' => 'client2@client.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'client2',
      "birth_date" => 'client2',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 2,
    ];
    $users[] = [
      'name' => 'coach',
      'email' => 'coach@coach.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'coach',
      "birth_date" => 'coach',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 3,
    ];
    $users[] = [
      'name' => 'coach1',
      'email' => 'coach1@coach.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'coach1',
      "birth_date" => 'coach1',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 3,
    ];
    $users[] = [
      'name' => 'assistant',
      'email' => 'assistant@assistant.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'assistant',
      "birth_date" => 'assistant',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 4,
    ];
    $users[] = [
      'name' => 'assistant1',
      'email' => 'assistant1@assistant.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'assistant1',
      "birth_date" => 'assistant1',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 4,
    ];
    $users[] = [
      'name' => 'intervenant',
      'email' => 'intervenant@assistant.com',
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      "first_name" => 'intervenant',
      "birth_date" => 'intervenant',
      "gender" => Arr::random(['female', 'male']),
      "address" => $faker->streetAddress,
      "postal_code" => $faker->postcode,
      "city" => $faker->city,
      "password" => Hash::make('password'),
      "additional_information" => $faker->sentences(6, true),
      "role_id" => 5,
    ];

    foreach ($users as $key => $user) {
      $role_id = $user['role_id'];
      unset($user['role_id']);
      $u = User::create($user);
      $u->roles()->attach($role_id);
    }
  }
}
