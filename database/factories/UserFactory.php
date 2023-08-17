<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = User::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $user_data = [
      'name' => $this->faker->name,
      'email' => $this->faker->unique()->safeEmail,
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),

      "first_name" => $this->faker->firstName,
      "birth_date" => $this->faker->date,
      "gender" => Arr::random(['female', 'male', 'child']),
      "address" => $this->faker->streetAddress,
      "postal_code" => $this->faker->postcode,
      "city" => $this->faker->city,
      "contact_origin" => Arr::random(['déjà venue', 'je suis passée devant', 'site internet', 'professionnels de santé', 'réseaux sociaux', 'bouche à oreille']),
      "password" => Hash::make('password'),
      "additional_information" => $this->faker->sentences(6, true),
      // "activities" => Arr::random(['aquajump', 'aquagym', 'aquabike'])
    ];

    return $user_data;
  }

  /**
   * Indicate that the model's email address should be unverified.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  public function unverified()
  {
    return $this->state(function (array $attributes) {
      return [
        'email_verified_at' => null,
      ];
    });
  }
}
