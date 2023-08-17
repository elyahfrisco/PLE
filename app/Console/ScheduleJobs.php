<?php

namespace App\Console;

use App\Models\Bill;
use App\Models\User;
use App\Models\Queue;
use App\Models\UserFee;
use App\Models\Planning;
use App\Models\UserPhone;
use App\Models\UserRelaunch;
use App\Jobs\SendRelaunchMailJob;
use App\Models\AbsencePrevention;
use Illuminate\Support\Facades\DB;
use App\Models\RecuperationRequest;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ScheduleJobs
{
  public static function dispatchRelaunchs()
  {
    UserRelaunch::query()
      ->where('date_relaunch', '<', now()->add('hour', 3))
      // ->whereDoesntHave('user', function ($user) {
      //     $user->whereRaw("email LIKE '%lesplaisirsdeleau.fr%'");
      // })
      ->get()->each(function ($relaunch) {
        SendRelaunchMailJob::dispatch($relaunch)->onQueue('user_relaunch');
      });
  }

  public static function clearJobsTable()
  {
    DB::table('jobs')->whereIn('id', DB::table('jobs')->limit(10000)->pluck('id'))->delete();
  }

  public static function clearDatabase()
  {
    User::onlyTrashed()->get()->each(function ($user_deleted) {
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');
      Queue::where('user_id', $user_deleted->id)->delete();
      AbsencePrevention::where('user_id', $user_deleted->id)->delete();
      Bill::where('user_id', $user_deleted->id)->delete();
      RecuperationRequest::where('user_id', $user_deleted->id)->delete();
      UserFee::where('user_id', $user_deleted->id)->delete();

      $user_deleted->subscriptions()->get()->each(function ($subscription) {
        $subscription->renewal()->delete();
        $subscription->activities()->delete();
        $subscription->delete();
      });
      $user_deleted->forceDelete();
      DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    });

    DB::statement("
        update
            subscription_activities
        set
            accomplished = NULL , session_status_txt = NULL , presence_status_txt = NULL, is_debited = NULL
        where
            absence_prevention_id is NULL
            and (
                is_debited = 0
                OR updated_at < '2021-09-02'
            )
            and (
                (
                    session_status_txt is NULL
                    OR (
                        accomplished = 0
                        OR accomplished is NULL
                    )
                )
                AND updated_at < '2021-09-02'
            )
        ");

    UserPhone::havingRaw('count(*) > 1')
      ->groupBy('phone', 'user_id')->get()->each(function ($phone) {
        UserPhone::where('phone', $phone->phone)
          ->where('id', '!=', $phone->id)
          ->where('user_id', $phone->user_id)
          ->delete();
      });

    DB::table('role_user')->havingRaw('count(*) > 1')
      ->groupBy('user_id', 'role_id')->get()->each(function ($user_role) {
        DB::table('role_user')->where('id', '!=', $user_role->id)
          ->where('user_id', $user_role->user_id)
          ->where('role_id', $user_role->role_id)
          ->delete();
      });
  }

  public static function updateKeySearchPlanning()
  {
    Planning::whereNull('search_key')->chunk(100, function ($plannings) {
      $plannings->each(function ($planning) {
        $planning->updateSearchKey();
        $planning->activitySessions->each(function ($activity_session) {
          $activity_session->updateSearchKey();
        });
      });
    });
  }

  public static function updateKeySearchPhone()
  {
    UserPhone::whereNull('search_key')->chunk(100, function ($phones) {
      $phones->each(function ($phone) {
        $phone->updateSearchKey();
      });
    });
  }

  public static function setFirstUsersSessions()
  {
    Subscription::orWhereDoesntHave('activities', function ($q) {
      $q->where('is_first', true);
    })->groupBy('user_id')->chunk(50, function ($subscriptions) {
      $subscriptions->each(function ($subscription) {
        $subscription->setFirstSession();
      });
    });
  }

  public static function cleanOldExportFile()
  {
    collect(Storage::allFiles('public/export/customer'))
      ->map(function ($file) {
        if (Carbon::parse(Storage::lastModified($file))->diffInDays(now()) >= 5)
          Storage::delete($file);
      });
  }
}
