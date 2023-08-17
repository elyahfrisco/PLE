<?php

namespace App\Models\Repositories;

use App\Models\ActivitySessions;
use App\Models\Pass;
use App\Models\Planning;
use App\Models\Price;
use App\Models\Renewal;
use App\Models\Subscription;
use App\Models\SubscriptionActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SessionsRepository
{
  public function getFilteredSessionsFor(Request $request)
  {
    $only_planning_id = $request->only_planning_id;
    $auth_user_is_coach = auth()->user()->isCoach();
    
    if ($request->renewal_for_subscription_id) {
      $subscription = $this->activitySessionsForRenewal($request->renewal_for_subscription_id);

      if ($subscription->renewal_status == 'continue') {
        if (intval($request->num_trimester) === 1) {
          $date  = date_create($subscription->time_start);

          $planning = Planning::where([
            'establishment_id' => $request->establishment_id,
            'season_id' => $request->season_id,
            'activity_id' => $subscription->activity_id,
            'time_start' => date_format($date, 'H:i:00'),
            'day' => strtolower(date_format($date, 'l')),
          ])
            ->whereDate('end_at', '>', now())
            ->orderBy('start_at')
            ->select('id')
            ->first();

          if (!$planning)
            return ['data' => []];

          $only_planning_id = $planning->id;
        } else {
          $only_planning_id = $subscription->planning_id;
        }
      } else if (
        in_array(
          $subscription->renewal_status,
          [
            'continue_change_time_else_stop',
            'continue_change_time'
          ]
        )
      ) {
        $only_planning_id = Renewal::find($request->renewal_id)->planning_id;;
      }
    }

    $query_activity_sessions = ActivitySessions::with(['activity'])
      ->orderBy('date', 'ASC')
      ->orderBy('activity_sessions.time_start', 'ASC')
      ->join('plannings', 'activity_sessions.planning_id', '=', "plannings.id");


      
      $this->handleStartAndEndDate($request, $query_activity_sessions);
      
      if ($auth_user_is_coach) {
        $query_activity_sessions
        ->with('participants');
      }
      
    

    if (!auth()->user()->isCustomer() && !auth()->user()->isProspect()) {
      $query_activity_sessions->JoinAbsences();

      $query_activity_sessions->withCount([
        'participants_pass_trimester' => fn ($query) => $query
          ->where('is_recuperation', false)
          ->join(
            'passes as p_',
            fn ($q) =>
            $q->on("subscription_activities.pass_id", '=', "p_.id")
              ->where("p_.pass_trimester", true)
          )
      ]);
    }

    if (!auth()->user()->isAdmin() && !request()->with_price) {
      if (!in_array(request()->view, ['years', 'year', 'month'])) {
        $query_activity_sessions->with([
          'absence_prevention',
          'absence_prevention.queue',
          'absence_prevention.queue.subscription_activity',
          'absence_prevention.queue.recuperation_request',
          'absence_prevention.queue.recuperation_request.session_for_catch_up',
        ]);
      }
    }

    
    
    if ($request->user_id) {
      $user = User::findInCache($request->user_id);
      if ($user->isCustomer()) {
        $query_activity_sessions->whereHas('participants', function ($query) {
          $query->where('users.id', request()->user_id);
        });

        // change activity_session_id
        $query_activity_sessions->withCount([
          'participants_pass_trimester' => fn ($query) => $query
            ->where('is_recuperation', false)
            ->join(
              'passes as p_',
              fn ($q) =>
              $q->on("subscription_activities.pass_id", '=', "p_.id")
                ->where("p_.pass_trimester", true)
            )
        ]);

        $query_activity_sessions->with('user_subscription_activity');

      } else {
        $query_activity_sessions
          ->leftJoin('activity_session_user', function ($join) {
            $join->on('activity_sessions.id', '=', 'activity_session_user.activity_session_id');
          })
          ->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'activity_session_user.user_id');
          })
          ->where('users.id',  auth()->user()->id);
      }
    } else if (auth()->user()->isCustomer() || auth()->user()->isProspect()) {
      
      //$query_activity_sessions->where('activity_sessions.hide_to_customer', false);

      // change activity_session_id
      $query_activity_sessions->withCount([
        'participants_pass_trimester' => fn ($query) => $query
          ->where('is_recuperation', false)
          ->join(
            'passes as p_',
            fn ($q) =>
            $q->on("subscription_activities.pass_id", '=', "p_.id")
              ->where("p_.pass_trimester", true)
          )
      ]);
    }

    if ($request->user_connected && auth()->user()) {
      if (auth()->user()->isCustomer()) {
        $query_activity_sessions
          ->whereHas(
            'participants',
            fn ($query) => $query->where('users.id',  auth()->user()->id)
          );
      } else {
        $query_activity_sessions->whereHas(
          'coachs',
          fn ($query) => $query->where('users.id',  auth()->user()->id)
        );
      }
    }

    $query_activity_sessions
      ->whereValueNotNullAndEqualTo([
        'activity_sessions.id' => $request->id,
        'activity_sessions.establishment_id' => $request->establishment_id,
        'activity_sessions.planning_id' => $only_planning_id,
      ])
      ->whereValueNotNullAndGTETo([
        'activity_sessions.id' => $request->min_id,
      ])
      ->whereValueNotNullAndLTETo([
        'activity_sessions.id' => $request->max_id,
      ]);

    if (is_numeric($request->num_trimester)) {
      $query_activity_sessions->WTrimester($request->season_id, $request->num_trimester);
    }

    if (is_numeric($request->season_id)) {
      $query_activity_sessions
        ->join('seasons', 'plannings.season_id', '=', "seasons.id")
        ->whereRaw("(activity_sessions.date >= seasons.date_start AND activity_sessions.date <= seasons.date_end)");
    }

    if ($request->participant_min) {
      $query_activity_sessions->has('participantsNoRelation', '>=', intval($request->participant_min));
    }

    if ($request->participant_max) {
      $query_activity_sessions->has('participantsNoRelation', '<=', intval($request->participant_max));
    }

    
    if ($request->subscribed_pass && is_numeric($request->pass_id)) {
      $query_activity_sessions
        ->whereIn(
          'activity_sessions.id',
          SubscriptionActivity::where('pass_id', $request->pass_id)
            ->where('user_id', $request->user_id)
            ->pluck('activity_session_id')
        );
    } else if (is_numeric($request->pass_id)) {
      $pass = Pass::findInCache($request->pass_id);

      $query_activity_sessions
        ->whereIn('activity_sessions.activity_id', $pass->activities_id);

      if (is_date($request->expire_date) && is_numeric($pass->period_validity)) {
        $query_activity_sessions
          ->where('activity_sessions.date', "<=", Carbon::parse($request->expire_date)->format('Y-m-d'));
      }
    }

    if (is_numeric($request->activity_id)) {
      $query_activity_sessions->where('activity_sessions.activity_id', $request->activity_id);
    }

    if ($request->not_session_user_id) {
      $query_activity_sessions->whereNotIn(
        'activity_sessions.id',
        SubscriptionActivity::userActivitySessionsId($request->not_session_user_id)
      );
    }

    /** Absence prevénu */
    if ($request->absence_notified) {
      $query_activity_sessions
        ->whereHas(
          'absences_prevention',
          fn ($query) => $query->where('user_id', $request->user_id)
        );
    }

    if ($request->without_prevention) {
      $query_activity_sessions
        ->whereDoesntHave(
          'absences_prevention',
          fn ($query) => $query->where('user_id', $request->user_id)
        );
    }
    /** Absence prevénu */

    if ($request->not_session_id && is_array($request->not_session_id)) {
      $query_activity_sessions
        ->whereNotIn('activity_sessions.id', $request->not_session_id);
    }

    if (is_numeric($request->activity_session_id_to_catch_up)) {
      $activity_session = ActivitySessions::find($request->activity_session_id_to_catch_up);

      $query_activity_sessions
        ->whereIn(
          'activity_sessions.activity_id',
          $activity_session->activity
            ->activities_for_recuperation_id()
        );

      $query_activity_sessions
        ->where('activity_sessions.id', '<>', $request->activity_session_id_to_catch_up);
    }

    if (is_array($request->selected_subscription_sessions_id_to_replace)) {
      $query_activity_sessions
        ->whereIn(
          'activity_sessions.activity_id',
          DB::table('subscription_activities')
            ->whereIn(
              'subscription_activities.id',
              $request->selected_subscription_sessions_id_to_replace
            )
            ->groupBy('subscription_activities.activity_id')
            ->select('subscription_activities.activity_id')
        );
    }

    if ($request->paged) {
      $results = $query_activity_sessions
        ->paginate(is_numeric($request->per_page) ? $request->per_page : 9);
    } else {
      $results = $query_activity_sessions->get();
      if (!$auth_user_is_coach) {
        $results->loadCount('participants');
      }
    }

    if (request()->with_price && !in_array(request()->view, ['years', 'year', 'month'])) {
      $q = DB::query()
        ->select(DB::raw("'_' key_, '_' num_trimester,'_' pass_id,'_' season_id, '_' price"));

      foreach ($results as $key => $session) {
        switch (request()->reduction) {
          case 'reduced':
            $type_of_fees_value = "prices.reduced_price";
            break;
          case 'reduced2':
            $type_of_fees_value = "prices.reduced_price2";
            break;
          default:
            $type_of_fees_value = "prices.price";
            break;
        }

        if (request()->pass_type == "trimester" || request()->pass_type == "one_session") {
          $q_p = Price::relatedTables()
            ->where('establishment_id', request()->establishment_id)
            ->where('activity_id', $session->activity_id)
            ->where('prices.season_id', $request->season_id)
            ->select(DB::raw("'$key' key_, trimesters.num_trimester, prices.pass_id, prices.season_id, $type_of_fees_value price"));

          if (request()->pass_type == "one_session") {
            $q_p->where('prices.pass_id', request()->pass_id);
          } elseif (request()->pass_type == "trimester") {
            $q_p->whereNotNull('prices.trimester_id')
              ->where('trimesters.num_trimester', $request->num_trimester)
              ->groupBy('trimesters.num_trimester');
          }

          $q->union($q_p);
        }
      }

      $prices_r = $q->get();

      unset($prices_r[0]);

      foreach ($prices_r as $key => $price) {
        $results[$price->key_]->price = $price;
      }

      // foreach ($r as $key => $session) {
      //     $r[$key]->price = $prices_r[$key + 1] ?? [];
      // }
    }

    return $results;
  }

  private function activitySessionsForRenewal($renewal_for_subscription_id)
  {
    return Subscription::query()
      ->leftJoin('renewals', 'renewals.subscription_id', 'user_subscriptions.id')
      ->join('subscription_activities', 'subscription_activities.subscription_id', 'user_subscriptions.id')
      ->orderBy('subscription_activities.created_at')
      ->groupBy('user_subscriptions.id')
      ->where('user_subscriptions.id', $renewal_for_subscription_id)
      ->selectRaw('subscription_activities.time_start, subscription_activities.planning_id, subscription_activities.activity_id, renewals.renewal_status')
      ->first();
  }

  private function handleStartAndEndDate($request, $query)
  {
    $date_start = null;
    $date_end = null;

    if ($request->minDate) {
      $date_start = Carbon::parse($request->minDate);
    }

    if ($request->maxDate) {
      $date_end = Carbon::parse($request->maxDate);
    }

    if (!$request->ignore_when_trimester_filter || !($request->ignore_when_trimester_filter && is_numeric($request->num_trimester))) {
      $request_start = Carbon::parse($request->start);
      if (is_date($request->start) && (!$date_start || $date_start->lessThan($request_start))) {
        $date_start = $request_start;
      }
      $request_end = Carbon::parse($request->end);
      if (is_date($request->end) && (!$date_end || $date_end->greaterThan($request_end))) {
        $date_end = $request_end;
      }
    }

    if (!$request->min_id && $date_start) {
      $query->where('activity_sessions.date', '>=', $date_start->format('Y-m-d'));
    }

    if (!$request->max_id && $date_end) {
      $query->where('activity_sessions.date', '<=', $date_end->format('Y-m-d'));
    }
  }
}
