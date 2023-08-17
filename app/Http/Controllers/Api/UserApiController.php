<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivitySessions;
use App\Models\FollowedUser;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserWish;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserApiController extends Controller
{
  public function search()
  {
    $q = request()->q;
    $users = User::query();
    // dd(request()->role);
    if (request()->role == 'coach') {
      $users = $users->coachRole();
    } elseif (is_array(request()->role)) {

      $users = $users->where(function ($query) {
        if (in_array('coach', request()->role)) {
          $query->orWhere(function ($query) {
            $query->coachRole();
          });
        }
        if (in_array('customer', request()->role)) {
          $query->orWhere(function ($query) {
            $query->customerRole();
          });
        }
        if (in_array('prospect', request()->role)) {
          $query->orWhere(function ($query) {
            $query->prospectRole();
          });
        }
      });

    } elseif (request()->role !== 'all') {
      $users = $users->customerRole();
    }

    if ($q) {
      $attrs = ['name', 'first_name', 'email', 'mail1', 'mail2'];

      if (request()->attr) {
        $attrs = request()->attr;
      }

      $users->where(function ($query) use ($q, $attrs) {
        foreach ($attrs as $key => $attr) {
          $query->orWhereRaw("LOWER(users.$attr) LIKE LOWER('$q')")
            ->orWhereRaw("LOWER(users.$attr) LIKE LOWER('%$q%')")
            ->orWhereRaw("LOWER(users.$attr) LIKE LOWER('$q%')")
            ->orWhereRaw("LOWER(users.$attr) LIKE LOWER('%$q')");

          if ($attr == 'name') {
            $query->orWhereRaw("(LOWER(CONCAT(users.first_name, ' ', name)) LIKE LOWER('$q'))");
            $query->orWhereRaw("(LOWER(CONCAT(users.first_name, ' ', name)) LIKE LOWER('%$q%'))");
            $query->orWhereRaw("(LOWER(CONCAT(users.first_name, ' ', name)) LIKE LOWER('$q%'))");
            $query->orWhereRaw("(LOWER(CONCAT(users.first_name, ' ', name)) LIKE LOWER('%$q'))");
            $query->orWhereRaw("(LOWER(CONCAT(users.name, ' ', first_name)) LIKE LOWER('%$q%'))");
            $query->orWhereRaw("(LOWER(CONCAT(users.name, ' ', first_name)) LIKE LOWER('$q%'))");
            $query->orWhereRaw("(LOWER(CONCAT(users.name, ' ', first_name)) LIKE LOWER('%$q'))");
          }
        }
      });

      if (request()->not_followed_by_user_id) {
        $users->whereNotIn('users.id', function ($query) {
          $query->from('followed_user')->where('user_follower_id', request()->not_followed_by_user_id)->select('user_following_id');
        });
        $users->where('users.id', '<>', request()->not_followed_by_user_id);
      }
    }

    if ((request()->not_participant_in_session)) {
      $users->whereDoesntHave('subscription_activities', function ($query) {
        $query->where('activity_session_id', request()->not_participant_in_session);
      });
    }

    $result = $users
      ->select('users.id', 'first_name', 'name', 'email', 'gender')
      ->paginate(request()->per_page ? request()->per_page : 20);

    return $result;
  }

  public function attente(Request $request)
  {
    if ($request->id) {
      $user = User::find($request->id);
      $user->activated = 2;
      $user->save();

      return true;
    }
  }

  /** get frais d'inscription | gestion */
  public function fees(Request $request)
  {
    if (!is_numeric($request->user_id)) {
      return $request->check ? null : [];
    }

    $user_fees = User::find($request->user_id)->fees();

    if ($request->type) {
      $user_fees->where('type', $request->type);
    }

    if ($request->season_id) {
      $user_fees->where('season_id', $request->season_id);
    }

    if ($request->with_bill || $request->check_paid) {
      $user_fees->with('bill');
      $user_fees->with('bill.payment');
    }

    /** si pour verification seulement */
    if ($request->check) {
      $r = $user_fees->first();
      if ($request->check_paid) {
        return $r && $r->bill->payment ? 1 : 0;
      } else {
        return $r ? 1 : 0;
      }
    }

    return $user_fees->get();
  }

  public function followings()
  {
    $followings = [];
    if (request()->user_id) {
      $followings = User::find(request()->user_id)->followings();
    } elseif (request()->requests_list) {
      if (request()->user_id) {
        $followings = User::find(request()->user_id)->followings();
      } else {
        $followings = FollowedUser::with(['follower', 'following'])
          ->whereHas('following')
          ->orderByRaw('accepted ASC, created_at DESC, acceptation_date DESC');
      }
    }
    if ($followings !== []) {
      return $followings->paginate(20);
    }
    return $followings;
  }

  public function establishments()
  {
    return Cache::remember('user_establishments_' . request()->user_id, 60 * 60 * 12, function () {
      return ActivitySessions::join('establishments', 'activity_sessions.establishment_id', 'establishments.id')
        ->join('subscription_activities', function ($query_subscription_activities) {
          $query_subscription_activities
            ->on('activity_sessions.id', 'subscription_activities.activity_session_id')
            ->where('subscription_activities.user_id', request()->user_id);
        })
        ->groupBy('activity_sessions.establishment_id')
        ->selectRaw('activity_sessions.id, activity_sessions.establishment_id, MAX(activity_sessions.time_end) time_end_max, establishments.name establishment_name')
        ->get()
        ->map(function ($activity_sessions) {
          return [
            'name' => $activity_sessions->establishment_name,
            'id' => $activity_sessions->establishment_id,
            'time_end_max' => $activity_sessions->time_end_max,
          ];
        });
    });
  }

  public function passes(Request $request)
  {
    $passes = Subscription::with('pass', 'establishment')->where('user_id', request()->user_id)->groupByRaw('establishment_id, pass_id')->get();

    foreach ($passes as $key => $value) {
      $passes[$key] = [
        'name' => $passes[$key]->pass->name,
        'id' => $passes[$key]->pass->id,
        'establishment' => $passes[$key]->establishment,
      ];
    }
    return $passes;
  }

  public function activities(Request $request)
  {
    // dd(UserWish::whereNotNull('user_wishes.time_start')->select('user_wishes.*')->joinPlanning()->where('user_wishes.time_start', '<>', 'plannings.time_start')->get()->each(function ($wish) {
    //     $planning = Planning::where([
    //         'day' => $wish->day,
    //         'time_start' => $wish->time_start,
    //         'time_end' => $wish->time_end,
    //         'activity_id' => $wish->activity_id,
    //         'establishment_id' => $wish->establishment_id,
    //     ])
    //     ->whereDate('plannings.end_at', '>=', 'user_wishes.created_at')->first();
    //     if ($planning && $wish->planning_id != $planning->id) {
    //         dump($planning->toArray());
    //         dump($wish->toArray());
    //         dump('---');
    //     }
    // }));

    return Cache::remember(
      'user_activities_' . request()->user_id . (request()->wished === "1" ? '_wished' : null),
      60 * 60 * 12,
      function () {
        if (request()->wished === "1") {
          $activities = UserWish::where('user_id', request()->user_id)->get();
        } else {
          $activities = ActivitySessions::with('activity')->whereHas('participantsNoRecuperations', function ($query) {
            $query->where('users.id', request()->user_id);
            $query->whereDate('time_end', '>=', Carbon::now());
          })->groupByRaw('establishment_id, planning_id, activity_id')
            ->JoinEstablishment()
            ->get();
        }

        foreach ($activities as $key => $value) {
          $group_name = null;
          $establishment = null;

          if ($activities[$key]->establishment) {
            $group_name = ($activities[$key]->group_name ?? optional($activities[$key]->planning)->group_name);
            $establishment = $activities[$key]->establishment->name;
          }

          $activities[$key] = [
            'name' => $activities[$key]->activity->name,
            'id' => $activities[$key]->activity->id,
            'establishment' => $establishment,
            'group_name' => $group_name,
            'planning_id' => $activities[$key]->planning_id,
            'establishment_id' => $activities[$key]->establishment_id,
            'activity_session_id' => $activities[$key]->id,
          ];
        }
        return $activities;
      }
    );
  }
  public function prospectCount()
  {
    return Cache::remember('prospect_count', 60, function () {
      return User::prospectRole()->count();
    });
  }
}
