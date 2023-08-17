<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Establishment;
use App\Models\AbsencePrevention;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionActivity;
use Illuminate\Support\Facades\Auth;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ActivitySessions extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'date',
    'time_start',
    'time_end',
    'shifted',
    'shift_date',
    'max_effective',
    'super_pass',
    'accomplished',
    'establishment_id',
    'season_id',
    'planning_id',
    'activity_id',
    'presence_checked_at',
    'hide_to_customer',
  ];

  protected $appends = ['elapseTime', 'elapseTimePresence', 'TimeSpent', 'real_max_effective', 'group_name'];

  public function scopeFilter($query, $date_start, $date_end)
  {
    $date_start = date_format(date_create($date_start), 'Y-m-d');
    $date_end = date_format(date_create($date_end), 'Y-m-d');
    return $query->whereDate('activity_sessions.date', '>=', $date_start)->whereDate('activity_sessions.date', '<=', $date_end);
  }

  public function scopeLastWeekSession($query)
  {
    $lastSessionDate = (clone $query)->latest()->first();

    if ($lastSessionDate) {
      $date = Carbon::parse($lastSessionDate->date);

      $date->startOfWeek();
      $week_start = clone $date;
      $date->endOfWeek();
      $week_end = clone $date;

      $query->where('activity_sessions.date', '>=', $week_start)->where('activity_sessions.date', '<=', $week_end);
    }
    return $query;
  }

  public function planning()
  {
    return $this->belongsTo(Planning::class);
  }

  public function activity()
  {
    return $this->belongsTo(Activity::class);
  }

  public function establishment()
  {
    return $this->belongsTo(Establishment::class);
  }

  public function participants_pass_trimester()
  {
    return $this->participants();
  }

  public function participants()
  {
    return $this->belongsToMany(User::class, 'subscription_activities', 'activity_session_id')
      ->leftJoin('passes', function ($q) {
        $q->on("subscription_activities.pass_id", '=', "passes.id")
          ->on("subscription_activities.user_id", '=', "users.id");
      })
      ->leftJoin('user_subscriptions', function ($q) {
        $q->on("user_subscriptions.id", '=', "subscription_activities.subscription_id");
      })
      ->leftJoin('renewals', function ($q) {
        $q->on("user_subscriptions.id", '=', "renewals.subscription_id");
      })
      ->leftJoin('payments', function ($q) {
        $q->on("payments.bill_id", '=', "user_subscriptions.bill_id");
      })
      ->leftJoin('queues', function ($q) {
        $q->on("queues.absence_prevention_id", '=', "subscription_activities.absence_prevention_id");
      })
      ->leftJoin('recuperation_requests', function ($q) {
        $q->on("recuperation_requests.queue_id", '=', "queues.id");
        $q->whereRaw("recuperation_requests.deleted_at IS NULL");
      })
      ->leftJoin('user_subscriptions as renewal_user_subscription', function ($q) {
        $q->on("renewal_user_subscription.id", '=', "user_subscriptions.renewal_subscription_id");
      })
      ->leftJoin('payments as renewal_user_subscription_payment', function ($q) {
        $q->on("renewal_user_subscription_payment.bill_id", '=', "renewal_user_subscription.bill_id");
      })
      ->select(DB::raw('
            users.*,
            passes.name pass_name,
            passes.id pass_id,
            passes.pass_trimester pass_trimester,
            user_subscriptions.id subscription_id,
            user_subscriptions.renewal_subscription_id renewal_subscription_id,
            user_subscriptions.renewal_id subscribed_renewal_id,
            user_subscriptions.bill_id,
            subscription_activities.id user_session_id,
            subscription_activities.accomplished,
            subscription_activities.is_recuperation,
            subscription_activities.absence_prevention_id,
            subscription_activities.absence_prevention_date,
            subscription_activities.is_debited,
            subscription_activities.queued_at,
            subscription_activities.can_catch_up_until,
            subscription_activities.session_status_txt,
            subscription_activities.presence_status_txt,
            subscription_activities.is_first as is_first_session,
            queues.id queue_id,
            recuperation_requests.presence_confirmed_at,
            recuperation_requests.id recuperation_request_id,
            recuperation_requests.presence_confirmed_at,
            recuperation_requests.activity_session_id_for_catch_up,
            payments.id as payed,
            renewal_user_subscription_payment.id as renewal_subscription_payed,
            renewal_user_subscription.bill_id as renewal_subscription_bill_id,
            renewals.renewal_status,
            renewals.id renewal_id,
            renewals.season_id renewal_season_id,
            renewals.establishment_id renewal_establishment_id,
            renewals.num_trimester renewal_num_trimester
            '))
      ->groupBy('users.id');
  }

  public function participantsNoRecuperations()
  {
    return $this->belongsToMany(User::class, 'subscription_activities', 'activity_session_id')
      ->leftJoin('passes', function ($q) {
        $q->on("subscription_activities.pass_id", '=', "passes.id")
          ->on("subscription_activities.user_id", '=', "users.id");
      })
      ->leftJoin('user_subscriptions', function ($q) {
        $q->on("user_subscriptions.id", '=', "subscription_activities.subscription_id");
      })
      ->leftJoin('renewals', function ($q) {
        $q->on("user_subscriptions.id", '=', "renewals.subscription_id");
      })
      ->leftJoin('payments', function ($q) {
        $q->on("payments.bill_id", '=', "user_subscriptions.bill_id");
      })
      ->leftJoin('queues', function ($q) {
        $q->on("queues.absence_prevention_id", '=', "subscription_activities.absence_prevention_id");
      })
      ->leftJoin('recuperation_requests', function ($q) {
        $q->on("recuperation_requests.queue_id", '=', "queues.id");
        $q->whereRaw("recuperation_requests.deleted_at IS NULL");
      })
      ->where('subscription_activities.is_recuperation', 0)
      ->select(DB::raw('
            users.*,
            passes.name pass_name,
            passes.id pass_id,
            passes.pass_trimester pass_trimester,
            user_subscriptions.id subscription_id,
            user_subscriptions.renewal_subscription_id renewal_subscription_id,
            user_subscriptions.renewal_id subscribed_renewal_id,
            user_subscriptions.bill_id,
            subscription_activities.id user_session_id,
            subscription_activities.accomplished,
            subscription_activities.is_recuperation,
            subscription_activities.absence_prevention_id,
            subscription_activities.absence_prevention_date,
            subscription_activities.is_debited,
            subscription_activities.queued_at,
            subscription_activities.can_catch_up_until,
            subscription_activities.session_status_txt,
            subscription_activities.presence_status_txt,
            subscription_activities.is_first as is_first_session,
            queues.id queue_id,
            recuperation_requests.presence_confirmed_at,
            recuperation_requests.id recuperation_request_id,
            recuperation_requests.presence_confirmed_at,
            recuperation_requests.activity_session_id_for_catch_up,
            payments.id as payed,
            renewals.renewal_status,
            renewals.id renewal_id,
            renewals.season_id renewal_season_id,
            renewals.establishment_id renewal_establishment_id,
            renewals.num_trimester renewal_num_trimester
            '))
      ->groupBy('users.id');
  }

  public function participantsNoRelation()
  {
    return $this->belongsToMany(User::class, 'subscription_activities', 'activity_session_id');
  }

  public function user_subscription_activity()
  {
    return $this->hasOne(SubscriptionActivity::class, 'activity_session_id')->where('user_id', request()->user_id);
  }

  public function pointings()
  {
    return $this->belongsToMany(User::class, 'user_pointings', 'activity_session_id')->withPivot(['is_recuperation', '']);
  }

  public function coachs()
  {
    return $this->belongsToMany(User::class, 'activity_session_user', 'activity_session_id', 'user_id')
      ->withPivot(['accomplished', 'absence_prevention_id']);
  }

  public function absences_prevention()
  {
    return $this->hasMany(AbsencePrevention::class, 'activity_session_id');
  }

  /** absence prévenu par l'utilisateur, lié à la séance */
  public function absence_prevention($user_id = null)
  {
    $user_id = request()->user_id ? request()->user_id : ($user_id ? $user_id : auth()->user()->id ?? null);
    return $this->hasOne(AbsencePrevention::class, 'activity_session_id')->where('user_id', $user_id);
  }

  public function getElapseTimeAttribute()
  {
    return Carbon::parse($this->date)->diffForHumans();
  }

  public function getElapseTimePresenceAttribute()
  {
    return Carbon::parse($this->presence_checked_at)->diffForHumans();
  }

  public function getTimeSpentAttribute()
  {
    return Carbon::parse($this->time_end) < now();
  }

  public function queue($user_id = null)
  {
    $user_id = request()->user_id ? request()->user_id : ($user_id ? $user_id : auth()->user()->id ?? null);
    return $this->hasOne(Queue::class, 'absence_prevention_id', 'absence_prevention_id')->where('user_id', $user_id);
  }


  /** Scope */

  public function scopeWTrimester($query, $season_id, $num_trimester)
  {
    $date = date('Y-m-d');

    $trimesterLimit =
      Cache::remember(
        to_cache_name(compact('season_id', 'num_trimester', 'date')),
        60 * 60 * 12,
        fn () => Trimester::where('season_id', $season_id)
          ->where('num_trimester', $num_trimester)
          ->where('date_end', '>', $date)
          ->orderBy('date_start')
          ->select(DB::raw('MIN(date_start) date_start, MAX(date_end) date_end'))
          ->first()
      );

    if ($trimesterLimit->date_start) {
      $query->where('activity_sessions.date', ">=", Carbon::parse($trimesterLimit->date_start)->format('Y-m-d'));
      $query->where('activity_sessions.date', "<=", Carbon::parse($trimesterLimit->date_end)->format('Y-m-d'));
    } else {
      /** forcer de ne renvoie aucun résultat */
      $query->whereRaw('1 = 2');
    }
  }

  public function scopeWActivity($query, $season_id, $num_trimester)
  {
    $trimesterLimit = Trimester::where('season_id', $season_id)
      ->where('num_trimester', $num_trimester)
      ->where('date_end', '>', now())
      ->select(DB::raw('MIN(date_start) date_start, MAX(date_end) date_end'))
      ->first();
    $query->where('activity_sessions.date', ">=", $trimesterLimit->date_start);
    $query->where('activity_sessions.date', "<=", $trimesterLimit->date_end);
    return $query;
  }

  public function scopeJoinSeason($query)
  {
    return $query->join('plannings', 'activity_sessions.planning_id', '=', "plannings.id")
      ->join('seasons', 'plannings.season_id', '=', "seasons.id");
  }

  public function getDayNameAttribute()
  {
    return substr(dToFr(Carbon::parse($this->time_start)->format('l')), 0, 2);
  }

  public function SigleEstablishmentName()
  {
    if ($this->relationLoaded('establishment') && $this->establishment) {
      return strtolower($this->establishment->name) == 'bordeaux' ? 'C' : $this->establishment->name[0];
    } else if ($this->establishment_name) {
      return strtolower($this->establishment_name) == 'bordeaux' ? 'C' : $this->establishment_name[0];
    } else {
      return "";
    }
  }

  public function getGroupNameAttribute()
  {
    return strtoupper($this->day_name) . Carbon::parse($this->time_start)->format('H\Hi') . $this->SigleEstablishmentName();
  }

  public function scopeJoinEstablishment($query)
  {
    $query->leftJoin('establishments', 'activity_sessions.establishment_id', 'establishments.id')
      ->selectRaw('activity_sessions.*')
      ->addSelect(DB::raw('establishments.name establishment_name'));
  }

  public function scopeJoinActivity($query)
  {
    $query->leftJoin('activities', 'activity_sessions.activity_id', 'activities.id')
      ->addSelect(DB::raw('activities.name activity_name'));
  }

  public function scopeJoinAbsences($query)
  {
    $query->leftJoin('subscription_activities', function ($join) {
      $join->on('activity_sessions.id', 'subscription_activities.activity_session_id')
        ->whereRaw('(subscription_activities.accomplished = 0 OR subscription_activities.absence_prevention_id IS NOT NULL )');
    })
      ->selectRaw('activity_sessions.*')
      ->groupBy('activity_sessions.id')
      ->addSelect(DB::raw('COUNT(subscription_activities.id) absence_count'));
  }

  // public function scopeJoinAbsencesPassTrimester($query)
  // {
  //     $query
  //         ->leftJoin('subscription_activities as s_a_pass_trimester', function ($join) {
  //             $join->on('activity_sessions.id', 's_a_pass_trimester.activity_session_id')
  //                 ->whereRaw('(s_a_pass_trimester.accomplished = 0 OR s_a_pass_trimester.absence_prevention_id IS NOT NULL )');
  //         })
  //         ->leftJoin(
  //             'passes as passes_trimesters',
  //             fn ($q) =>
  //             $q->on("s_a_pass_trimester.pass_id", '=', "passes_trimesters.id")
  //                 ->where("passes_trimesters.pass_trimester", true)
  //         )
  //         ->addSelect(DB::raw('COUNT(passes_trimesters.id) absence_count_pass_trimester'));
  // }

  public function GetRealMaxEffectiveAttribute()
  {
    return intval($this->max_effective) + intval($this->super_pass);
  }

  public function updateSearchKey()
  {
    $this->load('establishment', 'activity');
    $search_key = " $this->group_name " . dToFr(Carbon::parse($this->time_start)->format('l'));

    if ($search_key != $this->search_key) {
      $this->search_key = $search_key;
      $this->save();
      Log::info("activity_session $this->id : " . $this->search_key);
    }
  }

  protected static function boot()
  {
    parent::boot();

    static::created(function ($planning_session) {
      $planning_session->updateSearchKey();
    });
    static::updated(function ($planning_session) {
      $planning_session->updateSearchKey();
    });
  }
}
