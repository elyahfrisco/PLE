<?php

namespace App\Console;

use App\Models\Bill;
use App\Models\Role;
use App\Models\User;
use App\Models\Queue;
use App\Models\Renewal;
use App\Models\UserFee;
use App\Models\Planning;
use App\Models\UserPhone;
use App\Models\UserRelaunch;
use App\Console\ScheduleJobs;
use App\Jobs\SendRelaunchMailJob;
use App\Models\AbsencePrevention;
use Illuminate\Support\Facades\DB;
use App\Models\RecuperationRequest;
use Illuminate\Support\Facades\Log;
use App\Models\SubscriptionActivity;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * The Artisan commands provided by your application.
   *
   * @var array
   */
  protected $commands = [
    //
  ];

  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    /** clear cache */
    $schedule->command('cache:clear')
      ->daily()
      ->onSuccess(function () {
        Log::info("Schedule cache clear executed");
      })
      ->runInBackground();

    /** run export */
    $schedule->command('queue:work --queue=prepare_customers_export,export_chunked_customers,export_customers --stop-when-empty')
      ->onOneServer()
      ->everyTwoMinutes()
      ->runInBackground();

    /** execute subscription jobs */
    $schedule->command('queue:work --queue=subscription_jobs --stop-when-empty')
      ->onSuccess(function () {
        Log::info("Schedule subscription jobs executed" . date('H:i'));
      })
      ->everyThirtyMinutes()
      ->runInBackground();

    /** update key_search */
    $schedule->call(function () {
      ScheduleJobs::updateKeySearchPlanning();
    })
      ->when(Planning::whereNull('search_key')->count())
      ->onSuccess(function () {
        Log::info("Schedule add planning search_key executed");
      })
      ->daily()
      ->runInBackground();

    $schedule->call(function () {
      ScheduleJobs::updateKeySearchPhone();
    })
      ->when(UserPhone::whereNull('search_key')->count())
      ->onSuccess(function () {
        Log::info("Schedule add phone search_key executed");
      })
      ->daily()
      ->runInBackground();

    /** update users first sessions */
    $schedule->call(function () {
      ScheduleJobs::setFirstUsersSessions();
    })
      ->onSuccess(function () {
        Log::info("Schedule update users first sessions executed");
      })
      ->daily()
      ->runInBackground();

    /** clean Old Exported File */
    $schedule->call(function () {
      ScheduleJobs::cleanOldExportFile();
    })
      ->onSuccess(function () {
        Log::info("Schedule clean Old Export File executed");
      })
      ->daily()
      ->runInBackground();

    /** Telescope Data Pruning */
    $schedule->command('telescope:prune --hours=48')->daily();

    /** Dispatch User relaunch */
    // $schedule->call(function () {
    //     ScheduleJobs::dispatchRelaunchs();
    // })
    //     ->onSuccess(function () {
    //         Log::info("Schedule dispatch send relaunch executed");
    //     })
    //     ->everyMinute()
    //     ->runInBackground();

    /** Clear Table */
    /*$schedule->call(function () {
            ScheduleJobs::clearJobsTable();
        })
            ->onSuccess(function () {
                Log::info("Schedule clear jobs table executed");
            })
            ->everyMinute()
            ->runInBackground();*/

    // /** clear database */
    // $schedule->call(function () {
    //     ScheduleJobs::clearDatabase();
    // })
    //     ->everyTwoHours()
    //     ->onSuccess(function () {
    //         Log::info("Schedule database clear executed");
    //     })
    //     ->runInBackground();


    // /** send user relaunch */
    // $schedule->command('queue:work --queue=user_relaunch --stop-when-empty')
    //     ->onOneServer()
    //     ->withoutOverlapping()
    //     ->when(count_job('user_relaunch'))
    //     ->onSuccess(function () {
    //         Log::info("Schedule relaunch executed");
    //     })
    //     ->runInBackground();

    /** send user email */
    // $schedule->command('queue:work --queue=customer_emails --stop-when-empty')
    //     ->onOneServer()
    //     ->withoutOverlapping()
    //     ->when(count_job('customer_emails'))
    //     ->onSuccess(function () {
    //         Log::info("Schedule customer_emails executed");
    //     })
    //     ->runInBackground();


    /** run import */
    // $schedule->command('queue:work --queue=customer_import,subscribe_a_customer_to_pass --stop-when-empty')
    //     ->onOneServer()
    //     ->everyThreeMinutes()
    //     ->runInBackground();

  }

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands()
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
