<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Pass;
use App\Models\Season;
use App\Models\SubscriptionActivity;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Subscription extends AppModel
{
  use HasFactory;
  protected $table = 'user_subscriptions';
  protected $fillable = [
    'expired_at',
    'subscription_type',
    'amount',
    'user_id',
    'pass_id',
    'season_id',
    'payment_id',
    'establishment_id',
    'bill_id',
    'type_of_fees',
    'num_trimester',
    'number_of_sessions',
    'renewal_subscription_id',
    'renewal_id',
    'number_sessions_deleted',
  ];

  public function getPaymentStatusAttribute()
  {
    return $this->belongsTo(Establishment::class);
  }

  public function establishment()
  {
    return $this->belongsTo(Establishment::class);
  }

  public function season()
  {
    return $this->belongsTo(Season::class);
  }

  public function pass()
  {
    return $this->belongsTo(Pass::class);
  }

  public function customer()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function activities($ignore_activity = false)
  {
    return $this->hasMany(SubscriptionActivity::class)
      ->when(!$ignore_activity, function ($q) {
        return $q->with('activity');
      })
      ->orderBy('time_start');
  }

  public function first_activity()
  {
    return $this->hasOne(SubscriptionActivity::class)->orderBy('time_start')->take(1);
  }

  public function absence_preventions()
  {
    return $this->hasMany(SubscriptionActivity::class)
      ->orderBy('time_start');
  }

  public function comments()
  {
    return $this->hasMany(SubscriptionComment::class, 'user_subscription_id')
      ->with('author')->latest();
  }

  public function sessions()
  {
    return $this->hasMany(ActivitySessions::class, '')->orderBy('time_start');
  }

  public function payment()
  {
    return $this->belongsTo(Payment::class, 'bill_id', 'bill_id');
  }

  public function trimester()
  {
    return $this->hasOne(Trimester::class, 'num_trimester', 'num_trimester')
      ->where('season_id', $this->season_id);
  }

  public function bill()
  {
    return $this->hasOne(Bill::class, 'id', 'bill_id');
  }

  /*helper*/
  static function extractDateSessions($subscriptions)
  {
    if ($subscriptions) {

      foreach ($subscriptions as $key => $subscription_) {
        try {
          $first_activity_session = $subscription_->activities[0]['time_start'];
          $last_activity_session = $subscription_->activities[count($subscription_->activities) - 1]['time_end'];

          $day_count = Carbon::parse($first_activity_session)->diffInDays($last_activity_session, false) + 1;
          $days_left = now()->diffInDays($last_activity_session, false);

          if ($days_left > $day_count) {
            $days_left_percent = 0;
          } else {
            $days_left_percent = n_d2($days_left * 100 / $day_count);
          }

          if ($days_left_percent > 100) {
            $days_left_percent = 100;
          }

          $subscriptions[$key]->date = [
            "elapsetime_start" => Carbon::parse($first_activity_session)->diffForHumans(),
            "start" => $first_activity_session,
            "elapsetime_end" => Carbon::parse($last_activity_session)->diffForHumans(),
            "end" => $last_activity_session,
            "day_count" => $day_count,
            "days_left" => $days_left,
            "days_left_percent" => $days_left_percent,
          ];

          if (
            now() <= new \DateTime($last_activity_session)
            &&
            now() >= new \DateTime($first_activity_session)
          ) {
            $subscriptions[$key]->status = 'in_progress';
          } elseif (now() < new \DateTime($first_activity_session)) {
            $subscriptions[$key]->status = 'futur';
          } else {
            $subscriptions[$key]->status = 'finished';
          }

          $subscriptions[$key]->number_absence_prevention = 0;
          $subscriptions[$key]->number_recuperation = 0;

          foreach ($subscription_->activities as $activity_) {
            if ($activity_->absence_prevention_id) {
              $subscriptions[$key]->number_absence_prevention++;
            }
            if ($activity_->is_recuperation === 1) {
              $subscriptions[$key]->number_recuperation++;
            }
          }

          $subscriptions[$key]->number_absence_prevention += $subscription_->number_sessions_deleted;
        } catch (\Throwable $th) {
        }
      }
      return $subscriptions;
    }
    return [];
  }

  public function renewal()
  {
    return $this->hasOne(Renewal::class, 'subscription_id');
  }

  public function renewal_subscription()
  {
    return $this->hasOne(Subscription::class, 'renewal_subscription_id');
  }

  public function renewals()
  {
    return $this->hasMany(Renewal::class, 'subscription_id');
  }

  public function scopeSearch($query)
  {
    return $query->when(request()->q, function ($query, $q) {
      if (preg_match("/client_id/i", $q)) {
        $q_array = explode(':', $q);
        $key = $q_array[0];
        $q = $q_array[1] ?? '';

        switch ($key) {
          case "client_id":
            $query->where('user_subscriptions.user_id', $q);
            break;
        }
      } else {
        $query->WhereHas('customer', function ($query) use ($q) {
          $query->whereRaw("(
                        name LIKE '$q'
                    OR name LIKE '%$q%'
                    OR name LIKE '%$q'
                    OR name LIKE '$q%'
                    OR first_name LIKE '$q'
                    OR first_name LIKE '%$q%'
                    OR first_name LIKE '%$q'
                    OR first_name LIKE '$q%'
                    OR email LIKE '$q'
                    OR email LIKE '%$q%'
                    OR email LIKE '%$q'
                    OR email LIKE '$q%'
                    OR (LOWER(CONCAT(first_name, ' ', name)) LIKE LOWER('$q'))
                    OR (LOWER(CONCAT(first_name, ' ', name)) LIKE LOWER('%$q%'))
                    OR (LOWER(CONCAT(first_name, ' ', name)) LIKE LOWER('$q%'))
                    OR (LOWER(CONCAT(first_name, ' ', name)) LIKE LOWER('%$q'))
                    OR (LOWER(CONCAT(name, ' ', first_name)) LIKE LOWER('%$q%'))
                    OR (LOWER(CONCAT(name, ' ', first_name)) LIKE LOWER('$q%'))
                    OR (LOWER(CONCAT(name, ' ', first_name)) LIKE LOWER('%$q'))
                    )");
        });
        $query->OrWhereHas('activities.activity', function ($query) use ($q) {
          $query->whereRaw("(
                        name LIKE '%$q%'
                        )");
        });
      }
    });
  }

  public function scopeFilter($query)
  {
    if (is_numeric(request()->get('filterBy')['establishment_id'] ?? false)) {
      $query->where('user_subscriptions.establishment_id', request()->get('filterBy')['establishment_id']);
    }

    if (is_numeric(request()->get('filterBy')['season_id'] ?? false)) {
      $query->where('user_subscriptions.season_id', request()->get('filterBy')['season_id']);
    }

    if (is_numeric(request()->get('filterBy')['pass_id'] ?? false)) {
      $query->where('user_subscriptions.pass_id', request()->get('filterBy')['pass_id']);
    }

    if (is_numeric(request()->get('filterBy')['activity_id'] ?? false) && is_numeric(request()->get('filterBy')['planning_id'] ?? false)) {
      $query->whereHas('activities', function ($q) {
        $q->where('activity_id', request()->get('filterBy')['activity_id'])
          ->where('planning_id', request()->get('filterBy')['planning_id']);
      });
    } else if (is_numeric(request()->get('filterBy')['activity_id'] ?? false)) {
      $query->whereHas('activities', function ($q) {
        $q->where('activity_id', request()->get('filterBy')['activity_id']);
      });
    } else if (is_numeric(request()->get('filterBy')['planning_id'] ?? false)) {
      $query->whereHas('activities', function ($q) {
        $q->where('planning_id', request()->get('filterBy')['planning_id']);
      });
    }

    if (request()->get('filterBy')['renewed'] ?? false === 'true') {
      $query->whereHas('renewal', function ($query) {
        if (request()->get('filterBy')['renewal_status'] ?? false === 'true') {
          $query->where('renewal_status', request()->get('filterBy')['renewal_status']);
        }
      });
      if (request()->get('filterBy')['renewed_subscription_saved'] ?? false === 'true') {
        $query->whereNotNull('renewal_id');
      }
    }


    if (request()->get('filterBy')['subscription_status'] ?? false) {
      switch (request()->get('filterBy')['subscription_status']) {
        case 'in_progress':
          $query->whereRaw(("
                    (
                        exists ( select s_a.time_start FROM subscription_activities s_a WHERE s_a.subscription_id = user_subscriptions.id AND s_a.time_start <= NOW() ORDER BY s_a.time_start ASC LIMIT 1 )
                        AND
                        exists ( select s_a.time_end FROM subscription_activities s_a WHERE s_a.subscription_id = user_subscriptions.id AND s_a.time_end >= NOW() ORDER BY s_a.time_end DESC LIMIT 1 )
                    )
                    "));
          break;
        case 'futur':
          $query->whereRaw(("
                        ( select COUNT(s_a.time_start) FROM subscription_activities s_a WHERE s_a.subscription_id = user_subscriptions.id AND s_a.time_start > NOW() ORDER BY s_a.time_start ASC LIMIT 1 ) = 1
                    "));
          break;
        case 'finished':
          $query->whereRaw(("
                        ( select COUNT(s_a.time_end) FROM subscription_activities s_a WHERE s_a.subscription_id = user_subscriptions.id AND s_a.time_end < NOW() ORDER BY s_a.time_end DESC LIMIT 1 ) = 1
                    "));
          break;
      }
    }

    if (is_numeric(request()->get('filterBy')['subscription_type'] ?? false)) {
      $query->where('user_subscriptions.subscription_type', request()->get('filterBy')['subscription_type']);
    }
  }

  public function scopeOrder($query)
  {
    $keys = ['created_at', 'first_name', 'establishment_name', 'season_year_start', 'start_at'];

    return $query->when(request()->has('sortBy') && in_array(request()->sortBy, array_merge($this->fillable, $keys)), function ($query) {
      $direction = ((in_array(strtolower(request()->sortDirection), ['asc', 'desc'])) ? request()->sortDirection : 'asc');
      if (request()->get('sortBy') == 'first_name') {
        $query->leftJoin('users AS u_', 'u_.id', '=', 'user_subscriptions.user_id')
          ->orderBy('u_.first_name', $direction);
      } else if (request()->get('sortBy') == 'establishment_name') {
        $query->leftJoin('establishments AS e_', 'e_.id', '=', 'user_subscriptions.establishment_id')
          ->orderBy('e_.name', $direction);
      } else if (request()->get('sortBy') == 'season_year_start') {
        $query->leftJoin('seasons AS s_', 's_.id', '=', 'user_subscriptions.season_id')
          ->orderBy('s_.date_start', $direction);
      } else if (request()->get('sortBy') == 'start_at') {
        $query->leftJoin('subscription_activities AS sa_', 'sa_.subscription_id', '=', 'user_subscriptions.id')
          ->orderBy('sa_.time_start', $direction);
      } /* else if (request()->get('sortBy') == 'subscription_status') {
                $query->leftJoin('subscription_activities AS sa_', 'sa_.subscription_id', '=', 'user_subscriptions.id')
                    ->orderBy('sa_.time_end', $direction);
            } */ else {
        $query->orderBy(
          request()->sortBy,
          $direction
        );
      }
      $query->select('user_subscriptions.*');
    })->when(!request()->has('sortBy'), function ($query) {
      $query->orderByRaw('user_subscriptions.created_at DESC')
        ->select('user_subscriptions.*');
    });
  }

  public function setFirstSession()
  {
    if (!SubscriptionActivity::where(['user_id' => $this->user_id, 'is_first' => true])->exists()) {
      $first_activity_session = SubscriptionActivity::where('user_id', $this->user_id)->orderBy('time_start')->first('id');

      if ($first_activity_session)
        $first_activity_session->update(['is_first' => true]);
    }
  }

  public function updateDateCanCatchUpUntil()
  {
    /** Mise à jour des dernières dates de récuperation possible pour les seance du trimestre précédent dans la file d'attente */

    if ($this->num_trimester) {

      $lastSubscription  = Subscription::where('user_subscriptions.user_id', $this->user_id)
        ->where('user_subscriptions.establishment_id', $this->establishment_id)
        ->where('user_subscriptions.season_id', $this->season_id)
        ->whereNotNull('user_subscriptions.num_trimester')
        ->orderByRaw('user_subscriptions.season_id DESC, user_subscriptions.num_trimester DESC')
        ->first();

      $lastSubscriptionTrimester = Trimester::where('season_id', $lastSubscription->season_id)
        ->where('num_trimester', $lastSubscription->num_trimester)
        ->select(DB::raw('MIN(date_start) date_start, MAX(date_end) date_end'))
        ->first();

      if ($lastSubscriptionTrimester) {
        SubscriptionActivity::where('subscription_activities.user_id', $this->user_id)->where('subscription_activities.establishment_id', $this->establishment_id)->unrecoveredSessions()->update(['can_catch_up_until' => $lastSubscriptionTrimester->date_end]);
      }
    }
  }

  public function updateExpireDate()
  {
    /** Mise à jour de la dernière date autorisée pour les seances restantes à sélectionner, date d'expiration de la souscription */

    if ($this->pass->period_validity) {
      $period_validity_days = $this->pass->period_validity * 30;
      $first_activity_date = Carbon::parse($this->first_activity()->value('date'));

      $expire_date = ActivitySessions::where([
        'establishment_id' => $this->establishment_id,
      ])
        // ->whereIn('activity_id', $this->pass->activities()->select('activities.id'))
        ->where('date', '>=', $first_activity_date)
        ->take($period_validity_days)
        ->groupBy('date')
        ->pluck('date')
        ->last();
    } else {
      $expire_date = null;
    }

    $this->expired_at = $expire_date;
    $this->save();
  }

  protected static function boot()
  {
    parent::boot();

    static::deleting(function ($subscription) {
      Subscription::where('renewal_subscription_id', $subscription->id)->update([
        'renewal_subscription_id' => NULL,
        'renewal_id' => NULL,
      ]);
    });
  }
}
