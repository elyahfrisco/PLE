<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Pass;
use App\Models\User;
use App\Models\Season;
use App\Models\Activity;
use App\Models\Trimester;
use App\Models\UserPhone;
use Illuminate\Support\Arr;
use App\Models\PassCategory;
use App\Models\Establishment;
use Illuminate\Database\Seeder;
use Database\Seeders\PassSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\PriceSeeder;
use Database\Seeders\ActivitySeeder;
use Database\Seeders\ContactOriginSeeder;
use Database\Seeders\EstablishmentSeeder;
use Database\Seeders\ActivitySessionSeeder;
use App\Models\ActivitySubscriptionCondition;
use App\Models\ActivitySubscriptionQuestionnaire;
use App\Models\ActivitySubscriptionQuestionAnswer;

class DatabaseSeeder extends Seeder
{
  private $isLocal = false;

  public function __construct()
  {
    $this->isLocal = url("/") == "http://127.0.0.1:8000";
  }

  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {

    $faker = Factory::create();

    $this->call(EstablishmentSeeder::class);

    $establishments = Establishment::all();
    $establishment_count = count($establishments);

    $establishments->each(function ($establishment) use ($faker) {

      /* add seasons */
      for ($i = 0; $i < 1; $i++) {

        $latest_season = $establishment->seasons()->latest()->first();

        if ($latest_season) {
          $year_start = $latest_season->year_end;
          $year_end = $year_start + 1;

          $date_start = $latest_season->date_end->addMonths(2)->addDays(rand(1, 5));
          $date_end = $latest_season->date_end->addMonths(5)->addDays(rand(1, 5));
        } else {

          $year_start = rand(2020, 2021);
          $year_end = $year_start + 1;

          $date_rand = rand(1, 30);
          $date_rand = $date_rand < 10 ? "0$date_rand" : $date_rand;

          $date_start = date("$year_start-09-" . $date_rand);
          $date_end = date("Y-m-d", strtotime($date_start . ' + 10 months'));
        }

        $season = $establishment->seasons()->create([
          'date_start' => $date_start,
          'date_end' => $date_end,
          'year_start' => $year_start,
          'year_end' => $year_end,
        ]);

        /* add trimesters */
        $date_start_trimester = $season->date_start;
        $date_end_trimester = $season->date_start->addMonths(3);

        for ($j = 1; $j <= 3; $j++) {
          $season
            ->trimesters()
            ->create([
              'num_trimester' => $j,
              'week_count' => rand(15, 19),
              'date_start' => $date_start_trimester,
              'date_end' => $date_end_trimester,
            ]);

          // $trimester = Trimester::latest()->first();

          // for ($k = 0; $k < rand(1, 5); $k++) {

          //     $date_closing = $faker->dateTimeBetween($trimester->date_start, $trimester->date_end);

          //     $date_reopening = clone $date_closing;

          //     $closingData = [
          //         'date_start' => $date_closing,
          //         'date_end' => $date_reopening->modify('+ ' . rand(0, 3) . ' days')->format("Y-m-d"),
          //         'reason' => $faker->sentence(6),
          //     ];

          //     $trimester->closings()->create($closingData);
          // }

          $date_start_trimester->addMonths(3);
          $date_end_trimester->addMonths(3);
        }
      }
    });

    $this->call(ColorSeeder::class);
    $this->call(ActivitySeeder::class);

    Activity::all()->each(function ($activity) use ($establishment_count) {
      $activity->subscriptionConditions()->saveMany(ActivitySubscriptionCondition::factory(rand(1, 4))->make());
      $activity->subscriptionQuestionnaires()->saveMany(ActivitySubscriptionQuestionnaire::factory(rand(0, 3))->make());
      $activity->subscriptionQuestionnaires()->each(function ($question) {
        $question->answers()->saveMany(ActivitySubscriptionQuestionAnswer::factory(rand(0, 3))->make());
      });

      $q_establishment_ids = Establishment::inRandomOrder();

      if ($activity->care) {
        $q_establishment_ids->where('relaxation_center', true);
      }

      $q_establishment_ids->limit(rand(1, $establishment_count));

      $activity->establishments()->sync($q_establishment_ids->pluck('id'));
    });

    PassCategory::factory(rand(4, 8))
      ->create();

    $this->call(PassSeeder::class);

    Pass::all()->each(function ($pass) use ($establishment_count) {
      $q_establishment_ids = Establishment::inRandomOrder()->limit(rand(1, ($establishment_count - 1)));

      if ($pass->care) {
        $q_establishment_ids->where('relaxation_center', true);
      }

      $pass->establishments()->sync($q_establishment_ids->pluck('id'));

      $q_activity_ids = Activity::inRandomOrder()->limit(rand(1, Activity::count()));

      if ($pass->care) {
        $q_activity_ids->where('care', true);
      }

      $pass->activities()->sync($q_activity_ids->pluck('id'));
      $pass->update(['pass_category_id' => PassCategory::inRandomOrder()->first()->id]);
    });

    Establishment::all()->each(function ($establishment) use ($faker) {
      $passe_count = $establishment->loadCount('passes')->passes_count;
      $establishment->seasons()->each(function ($season) use ($establishment, $passe_count, $faker) {
        $ids_ = $establishment->passes()->inRandomOrder()->limit(rand(1, $passe_count))->pluck('id')->toArray();
        $ids = [];
        $establishment_id = $establishment->id;

        foreach ($ids_ as $key => $id) {
          $ids[$id] = ['establishment_id' => $establishment_id];
        }

        $season->passes()->sync($ids);

        /* Plannings */
        if ($this->isLocal) {
          $times_start = ["09:00", "08:30"];
          $delays_activity = ["30", "60", "90"];

          $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

          foreach ($days as $iday => $day) {

            $activities_ids = Activity::whereHas('passes', function ($query) use ($season) {
              $query->whereHas('establishments',  function ($query) use ($season) {
                $query->where('id', $season->establishment_id);
              });
            })->pluck('id');

            $time_start = Arr::random($times_start);

            $activities_ids = Arr::shuffle($activities_ids->toArray());

            foreach ($activities_ids as $key => $activity_id) {

              $time_end = date("H:i", strtotime(" +" . Arr::random($delays_activity) . " minutes", strtotime($time_start)));

              if (strtotime($time_end) > strtotime('22:00')) {
                break;
              }

              $start_at = $faker->dateTimeBetween($season->date_start, $season->date_end);

              $end_at = clone $start_at;

              $season->plannings()->create([
                'day' => $day,
                'time_start' => new \DateTime("$time_start:00"),
                'time_end' => new \DateTime("$time_end:00"),
                'start_at' => $start_at,
                'end_at' => $end_at->modify('+ ' . rand(5, 15) . ' weeks')->format("Y-m-d"),
                'max_effective' => rand(15, 20),
                'number_activity_sessions' => rand(15, 20),
                'trimester_num' => rand(1, 3),
                'activity_id' => $activity_id,
                'establishment_id' => $season->establishment_id,
              ]);

              $time_start = $time_end;
            }
          }
        }
      });
    });

    $this->call(PriceSeeder::class);
    $this->call(ContactOriginSeeder::class);

    $this->call(RoleSeeder::class);
    $this->call(UserSeeder::class);

    User::factory(30)
      ->hasPhones(rand(1, 3))
      ->hasComments(rand(1, 10))
      ->create();

    User::all()->each(function ($user) {
      $user->roles()->attach(1);
    });

    if (false) {
      $this->call(ActivitySessionSeeder::class);
    }
  }
}
