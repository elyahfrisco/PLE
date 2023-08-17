<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Queue extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'priority',
    'activity_session_id',
    'absence_prevention_id',
    'presence_confirmation_date',
    'presence_confirmed',
    'pass_id',
    'type',
    'user_id',
    'subscription_activity_id',
  ];

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function session_activity()
  {
    return $this->hasOne(ActivitySessions::class, 'id', 'activity_session_id');
  }

  public function recuperation_request()
  {
    return $this->hasOne(RecuperationRequest::class, 'queue_id', 'id');
  }

  public function subscription_activity()
  {
    return $this->hasOne(SubscriptionActivity::class, 'id', 'subscription_activity_id');
  }

  public function scopeWithRelations($query)
  {
    $query->with([
      'user',
      'recuperation_request',
      'recuperation_request.session_for_catch_up',
      'recuperation_request.session_for_catch_up.activity',
      'subscription_activity',
      'subscription_activity.session',
      'subscription_activity.absence_prevention',
      'subscription_activity.user_subscription.pass',
      'subscription_activity.session.activity',
      'subscription_activity.user_subscription',
    ])->wherehas('subscription_activity');

    if (is_numeric(request()->user_id) || !auth()->user()->isAdmin()) {
      $user_id = request()->user_id ?: auth()->user()->id;
      $query->where('user_id', $user_id);
    }
  }
  
  public function scopeFilter($query)
  {
    
    if (request()->pass_id) {
      $query->where('queues.pass_id', request()->pass_id);
    }

    if (request()->planning_id) {
      $query->whereHas(
        'session_activity',
        function ($q) {
          $q->where('activity_sessions.planning_id', request()->planning_id);
        }
      );
    }

    if (request()->activity_id) {
      $query->whereHas(
        'session_activity',
        function ($q) {
          $q->where('activity_sessions.activity_id', request()->activity_id);
        }
      );
    }

    if (request()->minDate) {
      $query->where('queues.created_at', '>=', request()->minDate);
    }

    if (request()->maxDate) {
      $query->where('queues.created_at', '<=', Carbon::parse(request()->maxDate)->add('day', 1));
    }
  }

  public function scopeSearch($query)
  {
    return $query->when(request()->q, function ($query, $q) {
      $query->WhereHas('user', function ($query) use ($q) {
        $query->whereRaw("(
                    name LIKE '%$q%'
                    OR first_name LIKE '%$q%'
                    OR email LIKE '%$q%'
                    OR (LOWER(CONCAT(first_name, ' ', name)) LIKE LOWER('%$q%'))
                    OR (LOWER(CONCAT(name, ' ', first_name)) LIKE LOWER('%$q%'))
                        )");
      })
        ->OrWhereHas('subscription_activity.activity', function ($query) use ($q) {
          $query->whereRaw("(
                    name LIKE '%$q%'
                        )");
        });
    });
  }

  public function scopeOrder($query)
  {
    return $query->when(request()->has('sortBy') && in_array(request()->sortBy, array_merge($this->fillable, ['created_at', 'first_name', 'activity_name', 'pass_name', 'presence_confirmed_at'])), function ($query) {
      $direction = ((in_array(strtolower(request()->sortDirection), ['asc', 'desc'])) ? request()->sortDirection : 'asc');
      if (request()->get('sortBy') == 'first_name') {
        $query->leftJoin('users AS u_', 'u_.id', '=', 'queues.user_id')
          ->orderBy('u_.first_name', $direction);
      } elseif (request()->get('sortBy') == 'activity_name') {
        $query->leftJoin('activity_sessions AS as_', 'as_.id', '=', 'queues.activity_session_id')
          ->leftJoin('activities AS a_', 'a_.id', '=', 'as_.activity_id')
          ->orderBy('a_.name', $direction);
      } elseif (request()->get('sortBy') == 'pass_name') {
        $query->leftJoin('passes AS p_', 'p_.id', '=', 'queues.pass_id')
          ->orderBy('p_.name', $direction);
      } elseif (request()->get('sortBy') == 'presence_confirmed_at') {
        $query->leftJoin('recuperation_requests AS rr_', 'rr_.queue_id', '=', 'queues.id')
          ->orderBy('rr_.presence_confirmed_at', $direction);
      } else {
        $query->orderBy(
          request()->sortBy,
          $direction
        );
      }
    })->when(!request()->has('sortBy'), function ($query) {
      $query->orderByRaw('queues.created_at DESC');
    });
  }
}
