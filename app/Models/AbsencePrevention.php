<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class AbsencePrevention extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'date',
    'reason',
    'motif',
    'activity_session_id',
    'user_id',
    'activity_session_time_start',
    'establishment_id',
    'activity_id',
    'pass_id',
    'season_id',
    'added_by_user_id',
    'created_at',
  ];

  protected $appends = ['ElapseTimeBeforeStart'];

  public function getElapseTimeBeforeStartAttribute($value)
  {
    return Carbon::parse($this->created_at)->diffForHumans($this->activity_session_time_start);
  }

  public function setDateAttribute($value)
  {
    $this->attributes['date'] = now();
  }

  public function activity_session()
  {
    return $this->hasOne(ActivitySessions::class, 'id', 'activity_session_id');
  }

  public function pass()
  {
    return $this->hasOne(Pass::class, 'id', 'pass_id');
  }

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function subscription_activity()
  {
    return $this->hasOne(SubscriptionActivity::class, 'activity_session_id', 'activity_session_id')
      ->where('user_id', $this->user_id);
  }

  public function queue()
  {
    return $this->hasOne(Queue::class, 'absence_prevention_id', 'id');
  }

  public function scopeSearch($query)
  {
    return $query->when(request()->q, function ($query, $q) {
      $query->whereRaw("(
                motif LIKE '%$q%'
                OR reason LIKE '%$q%'
                )");

      $query->OrWhereHas('user', function ($query) use ($q) {
        $query->whereRaw("(
                    name LIKE '%$q%'
                    OR first_name LIKE '%$q%'
                    OR email LIKE '%$q%'
                    OR (LOWER(CONCAT(first_name, ' ', name)) LIKE LOWER('%$q%'))
                    OR (LOWER(CONCAT(name, ' ', first_name)) LIKE LOWER('%$q%'))
                        )");
      });
    });
  }

  public function scopeOrder($query)
  {
    return $query->when(request()->has('sortBy') && in_array(request()->sortBy, array_merge($this->fillable, ['created_at', 'first_name', 'activity_name', 'pass_name'])), function ($query) {
      $direction = ((in_array(strtolower(request()->sortDirection), ['asc', 'desc'])) ? request()->sortDirection : 'asc');
      if (request()->get('sortBy') == 'first_name') {
        $query->leftJoin('users AS u_', 'u_.id', '=', 'absence_preventions.user_id')
          ->orderBy('u_.first_name', $direction);
      } elseif (request()->get('sortBy') == 'activity_name') {
        $query->leftJoin('activities AS a_', 'a_.id', '=', 'absence_preventions.activity_id')
          ->orderBy('a_.name', $direction);
      } elseif (request()->get('sortBy') == 'pass_name') {
        $query->leftJoin('passes AS p_', 'p_.id', '=', 'absence_preventions.pass_id')
          ->orderBy('p_.name', $direction);
      } elseif (request()->get('sortBy') == 'activity_session_time_start') {
        $query->orderBy(DB::raw('TIME(absence_preventions.activity_session_time_start)'), $direction);
      } else {
        $query->orderBy(
          request()->sortBy,
          $direction
        );
      }
    })->when(!request()->has('sortBy'), function ($query) {
      $query->orderByRaw('absence_preventions.created_at DESC');
    });
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($absence) {
      if (!$absence->added_by_user_id) {
        $absence->added_by_user_id =  auth()->user()->id ?? null;
      }
    });

    static::deleting(function ($absence) {
      optional(optional($absence->queue()->first())->recuperation_request())->delete();
      optional($absence->queue())->delete();
      $absence->subscription_activity()->update([
        "absence_prevention_id" => NULL,
        "absence_prevention_date" => NULL,
        "presence_status_txt" => NULL,
        "queued_at" => NULL,
        "session_status_txt" => NULL,
        "can_catch_up_until" => NULL,
        "is_debited" => NULL,
        "accomplished" => NULL,
      ]);
    });
  }
}
