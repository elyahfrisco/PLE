<?php

namespace App\Http\Controllers;

use App\Http\Resources\AbsenceResource;
use App\Jobs\CreateQueueJob;
use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\AbsencePrevention;
use App\Models\ActivitySessions;
use App\Models\Subscription;
use App\Models\SubscriptionActivity;
use App\Models\Trimester;
use Doctrine\Inflector\Rules\Substitution;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($user_id = null)
  {
    $user = $user_id ? User::find($user_id) : Auth::user();

    if (!$user) {
      return redirect('/login');
    }
    return Inertia::render('Absence/index_', compact('user'));
  }

  /**
   * Admin | Coach.
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function notified_absences($user_id = null)
  {
    return Inertia::render('Absence/Admin/index');
  }

  public function _notified_absences(Request $request)
  {
    $QueryAbsensPrevention = AbsencePrevention::selectRaw('absence_preventions.*, subscription_activities.can_catch_up_until, subscription_activities.presence_status_txt, subscription_activities.session_status_txt')
      ->with('activity_session', 'activity_session.activity', 'queue.recuperation_request', 'pass', 'user');

    $QueryAbsensPrevention->leftJoin('subscription_activities', 'subscription_activities.absence_prevention_id', '=', 'absence_preventions.id');

    if (is_numeric($request->establishment_id)) {
      $QueryAbsensPrevention->where('absence_preventions.establishment_id', $request->establishment_id);
    }

    if (intval($request->upcoming)) {
      $QueryAbsensPrevention->where('activity_session_time_start', '>=', date('Y-m-d H:i'));
    }

    if (is_date($request->maxDate)) {
      $QueryAbsensPrevention->where('time_start', '<=', $request->maxDate);
    }

    if (is_date($request->minDate)) {
      $QueryAbsensPrevention->where('time_start', '>=', $request->minDate);
    }

    if (is_numeric($request->activity_id)) {
      $QueryAbsensPrevention->where('absence_preventions.activity_id', $request->activity_id);
    }

    if (is_numeric($request->pass_id)) {
      $QueryAbsensPrevention->where('pass_id', $request->pass_id);
    }

    if (is_numeric($request->season_id)) {
      $QueryAbsensPrevention->where('season_id', $request->season_id);
    }

    if ($request->desc && !$request->sortBy) {
      $QueryAbsensPrevention->orderByDesc('created_at');
    }

    $AbsensPrevention = $QueryAbsensPrevention->search()->order()->paginate(page_limit());
    // dd($AbsensPrevention[0]->toArray());
    return AbsenceResource::collection($AbsensPrevention);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create($user_id = null)
  {
    $user = $user_id ? User::find($user_id) : Auth::user();
    // return Inertia::render('Absence/create', compact('user'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, $user_id = null)
  {

    $user_id = $user_id ?: $request->user_id;
    $user = $user_id ? User::find($user_id) : Auth::user();
    $data = $request->all();

    $data['created_at'] = $data['date'];
    $data['date'] = Carbon::parse($data['date'])->format('Y-m-d');
    $data['added_by_user_id'] = Auth::user()->id;

    $activity_session = ActivitySessions::find($request->activity_session_id);
    $data['activity_session_time_start'] = $activity_session->time_start;
    $data['establishment_id'] = $activity_session->establishment_id;
    $data['activity_id'] = $activity_session->activity_id;

    if ($user->isCustomer()) {
      $subscription_activity = SubscriptionActivity::where('user_id', $user->id)
        ->where('activity_session_id', $request->activity_session_id)
        ->first();

      if ($subscription_activity) {
        $user_subscription = $subscription_activity->user_subscription;
        $data['season_id'] = $user_subscription->season_id;
        $data['pass_id'] = $user_subscription->pass_id;
      }
    }

    $absence = $user->absences()->create($data);

    if ($user->isCustomer()) {

      $subscription_activity->absence_prevention_id = $absence->id;
      $subscription_activity->absence_prevention_date = $absence->created_at;

      $prevent_hour = Carbon::parse($absence->created_at)->diffInHours($subscription_activity->time_start, false);

      $season_id = $activity_session->planning()->first()->season_id;
      
      $future_trimester_subscribed = $user
        ->subscriptions()
        ->where('season_id', intval($season_id) + 1)
        ->whereNotNull('num_trimester')
        ->orderByRaw('num_trimester DESC, created_at DESC')->first();

      if ($future_trimester_subscribed) {
        $last_trimester_subscribed = $future_trimester_subscribed; 
      } else {
        $last_trimester_subscribed = $user
          ->subscriptions()
          ->where('season_id', $season_id)
          ->whereNotNull('num_trimester')
          ->orderByRaw('num_trimester DESC, created_at DESC')->first();
      }

      if ($last_trimester_subscribed) {
        $last_date_of_trimester_subscribed = $last_trimester_subscribed->trimester->date_end;
        $can_catch_up_until = $last_date_of_trimester_subscribed;
        $endOfMonth = $can_catch_up_until->endOfMonth();

        if ($endOfMonth->format('l') == "Saturday" || $endOfMonth->format('l') == "Sunday") {
          $endOfMonth = $endOfMonth->addDays(-1);
          if ($endOfMonth->format('l') == "Saturday" || $endOfMonth->format('l') == "Sunday") {
            $can_catch_up_until = $endOfMonth->addDays(-1);
          }
        } else {
          $can_catch_up_until = $endOfMonth;
        }
        $mois = $can_catch_up_until->format('m');
      } else {
        /** default value if trimester not exist */
        $can_catch_up_until = now()->addDays(30);
      }


      $QueueData = [
        [
          'activity_session_id' => $activity_session->id,
          'user_id' => $user->id,
        ],
        [
          'absence_prevention_id' => $absence->id,
          'pass_id' => $user_subscription->pass_id,
          'subscription_activity_id' => $subscription_activity->id,
          'type' => 'for_recuperation',
        ]
      ];

      if ($prevent_hour >= 6) {
        $status = 'prevent_before_6';
        $color = '#ffed4a';
        $subscription_activity->queued_at = now();
        $subscription_activity->session_status_txt = 'queued';
        $subscription_activity->can_catch_up_until = $can_catch_up_until;
        CreateQueueJob::dispatchAfterResponse($QueueData);
      } elseif (2 <= $prevent_hour && $prevent_hour < 6) {
        $status = 'prevent_out_of_time';
        $color = "#f6993f";
        if (3 <= $prevent_hour) {
          $subscription_activity->queued_at = now();
          $subscription_activity->session_status_txt = 'queued';
          $subscription_activity->can_catch_up_until = $can_catch_up_until;
          CreateQueueJob::dispatchAfterResponse($QueueData);
        } else {
          $subscription_activity->session_status_txt = 'debited';
        }
      } else {
        $status = 'absent';
        $color = "#fb4f38";
        $subscription_activity->session_status_txt = 'debited';
      }

      $subscription_activity->presence_status_txt = $status;
      $subscription_activity->accomplished = 0;
      $subscription_activity->save();

      if (is_numeric($request->subscription_id)) {
        $subscription = Subscription::find($request->subscription_id);
        if ($subscription) {
          $content = '<div style="background-color:' . $color . '"><h5>' . $data["motif"] . ':</h5><p>' . $data["reason"] . '</p></div>';
          $subscription->comments()->create(['content' => $content]);
        }
      }
    }

    return back()->with('info', "L'absence a été notifiée");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(AbsencePrevention $absence)
  {
    $absence->delete();
    $message = "L'absence a été supprimée";
    return wantInertia() ? back()->with('info', $message) : response($message);
  }
}
