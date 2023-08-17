<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
  $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('seed_activity_session_8_mail', function () {
  $activity_sessions =
    [
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 09:00:00",
        "time_end" => "2022-05-08 09:45:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 9,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:10:40",
        "updated_at" => "2021-10-31 01:00:30",
        "establishment_id" => 2,
        "planning_id" => 160,
        "activity_id" => 1,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI09H00E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 09:15:00",
        "time_end" => "2022-05-08 10:00:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 9,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:10:47",
        "updated_at" => "2021-10-31 01:00:31",
        "establishment_id" => 2,
        "planning_id" => 162,
        "activity_id" => 1,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI09H15E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 09:45:00",
        "time_end" => "2022-05-08 10:30:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 9,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:11:00",
        "updated_at" => "2021-10-31 01:00:32",
        "establishment_id" => 2,
        "planning_id" => 164,
        "activity_id" => 1,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI09H45E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 10:00:00",
        "time_end" => "2022-05-08 10:45:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 9,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:11:11",
        "updated_at" => "2021-10-31 01:00:33",
        "establishment_id" => 2,
        "planning_id" => 166,
        "activity_id" => 1,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI10H00E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 10:30:00",
        "time_end" => "2022-05-08 11:15:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 9,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:11:17",
        "updated_at" => "2021-10-31 01:00:33",
        "establishment_id" => 2,
        "planning_id" => 168,
        "activity_id" => 1,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI10H30E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 10:45:00",
        "time_end" => "2022-05-08 11:30:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 9,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:11:24",
        "updated_at" => "2021-10-31 01:00:34",
        "establishment_id" => 2,
        "planning_id" => 170,
        "activity_id" => 1,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI10H45E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 11:15:00",
        "time_end" => "2022-05-08 12:00:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 9,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:11:31",
        "updated_at" => "2021-10-31 01:00:37",
        "establishment_id" => 2,
        "planning_id" => 182,
        "activity_id" => 1,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI11H15E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 11:30:00",
        "time_end" => "2022-05-08 12:15:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 9,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:11:37",
        "updated_at" => "2021-10-31 01:00:38",
        "establishment_id" => 2,
        "planning_id" => 183,
        "activity_id" => 1,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI11H30E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 12:30:00",
        "time_end" => "2022-05-08 13:15:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 10,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:11:50",
        "updated_at" => "2021-10-31 01:00:38",
        "establishment_id" => 2,
        "planning_id" => 185,
        "activity_id" => 13,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI12H30E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 13:15:00",
        "time_end" => "2022-05-08 14:00:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 10,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:11:57",
        "updated_at" => "2021-10-31 01:00:39",
        "establishment_id" => 2,
        "planning_id" => 186,
        "activity_id" => 13,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI13H15E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 14:00:00",
        "time_end" => "2022-05-08 15:30:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 10,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:12:03",
        "updated_at" => "2021-10-31 01:00:39",
        "establishment_id" => 2,
        "planning_id" => 187,
        "activity_id" => 13,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI14H00E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 16:15:00",
        "time_end" => "2022-05-08 17:00:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 10,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:16:37",
        "updated_at" => "2021-10-31 01:00:40",
        "establishment_id" => 2,
        "planning_id" => 189,
        "activity_id" => 13,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI16H15E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 15:30:00",
        "time_end" => "2022-05-08 16:15:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 10,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:16:48",
        "updated_at" => "2021-10-31 01:00:39",
        "establishment_id" => 2,
        "planning_id" => 188,
        "activity_id" => 13,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI15H30E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 12:15:00",
        "time_end" => "2022-05-08 14:00:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 6,
        "accomplished" => 0,
        "created_at" => "2021-06-29 12:17:24",
        "updated_at" => "2021-10-31 01:00:38",
        "establishment_id" => 2,
        "planning_id" => 184,
        "activity_id" => 11,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI12H15E dimanche"
      ],
      [
        "date" => "2022-05-08",
        "time_start" => "2022-05-08 17:00:00",
        "time_end" => "2022-05-08 18:30:00",
        "shifted" => 0,
        "shift_date" => null,
        "max_effective" => 10,
        "accomplished" => 0,
        "created_at" => "2022-02-22 14:45:06",
        "updated_at" => "2022-02-22 14:45:06",
        "establishment_id" => 2,
        "planning_id" => 190,
        "activity_id" => 13,
        "super_pass" => 0,
        "presence_checked_at" => null,
        "hide_to_customer" => 0,
        "search_key" => " DI17H00E dimanche"
      ]
    ];
  foreach ($activity_sessions as $activity) {
    DB::connection('mysql')->table('activity_sessions')->insert($activity);
  }

  echo "Operation avec succes.";
});
