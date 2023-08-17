<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Establishment;
use App\Models\ActivitySessions;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Repositories\ActivitySessionRepository;
use App\Models\SubscriptionActivity;
use Illuminate\Support\Facades\Validator;

class ActivitySessionController extends Controller
{
  public $activitySessionRepository;

  public function __construct(ActivitySessionRepository $activitySessionRepository)
  {
    $this->activitySessionRepository = $activitySessionRepository;
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Establishment $establishment)
  {
    $data = compact('establishment');

    if (request()->all()) {
      $data = array_merge($data, request()->all());
    }

    return Inertia::render('Admin/Establishment/Planning/Session/index', $data);
  }

  public function participants(Establishment $establishment, ActivitySessions $activity_session)
  {
    $participants = $this
      ->activitySessionRepository
      ->getParticipantsOf($activity_session);

    $session = $activity_session;

    $session->loadCount([
      'participants_pass_trimester' => fn ($query) => $query
        ->where('is_recuperation', false)
        ->join(
          'passes',
          fn ($q) =>
          $q->on("subscription_activities.pass_id", '=', "passes.id")
            ->where("passes.pass_trimester", true)
        )
    ]);

    return Inertia::render('Admin/Establishment/Planning/Session/Participant/index', compact('session', 'participants'));
  }

  public function set_presence_status(Request $request, $user_session_id)
  {
    $is_debited = 1;

    $subscription_activity = SubscriptionActivity::find($user_session_id);

    $data = [
      'accomplished' => $request->accomplished,
      'is_debited' => $is_debited,
    ];

    if (!$subscription_activity->absence_prevention_id) {

      if ($request->accomplished) {
        $data['presence_status_txt'] = 'present';
        $data['session_status_txt'] = 'debited';
      } else {
        /** la séance est débitée du PASS, sans prevenance */
        $data['presence_status_txt'] = 'absent';
        $data['session_status_txt'] = 'debited';
      }
    }

    if ($request->accomplished == null) {
      DB::statement("
            update `subscription_activities`
            set `accomplished` = NULL, session_status_txt = NULL
            where `id` = $user_session_id
            ");
    } else {
      $subscription_activity->update($data);
    }

    if ($subscription_activity->absence_prevention_id) {
      $subscription_activity = SubscriptionActivity::find($user_session_id);
      if ($subscription_activity->accomplished === null || $subscription_activity->accomplished === 1) {
        optional($subscription_activity->absence_prevention()->first())->delete();
      }
    }

    /** mise à jour date de dernière présence */
    $subscription_activity->session()->first()->update(['presence_checked_at' => now()]);

    return response('ok');
  }

  public function set_presence_status_for_all_paticipant(Request $request, $activity_session_id)
  {
    $subscription_activity = SubscriptionActivity::where('activity_session_id', $activity_session_id);

    $subscription_activity->update(
      [
        'accomplished' => 1,
        'is_debited' => 1,
        'session_status_txt' => 'debited',
        'presence_status_txt' => 'present',
        /** la séance est débitée du PASS */
      ]
    );

    /** annulation absence prévenu */
    $subscription_activity->get()->each(function ($one_subscription_activity) {
      if ($one_subscription_activity->absence_prevention_id) {
        optional($one_subscription_activity->absence_prevention()->first())->delete();
      }
    });

    /** mise à jour date de dernière présence */
    $activity_session = $subscription_activity->first()->session()->first();
    $activity_session->update(['presence_checked_at' => now()]);
    $establishment_id = $activity_session->establishment_id;

    return redirect()
      ->route('establishments.plannings.sessions.participants', [
        'establishment' => $establishment_id,
        'activity_session' => $activity_session_id
      ])
      ->with('info', 'Présence effectuée');
  }

  public function setAccompished(Request $request, Establishment $establishment, $activity_session_id)
  {
    $activitySession = ActivitySessions::find($activity_session_id);
    $activitySession->update(['accomplished' => 1]);
    session()->flash('success', 'le statut de la séance a été changé en "séance effectué"');
    return back();
  }

  public function setCoachs(Request $request, $activity_session_id)
  {
    Validator::make($request->all(), [
      'coachs_id' => [
        'required',
        function ($attribute, $value, $fail) {
          if (count($value) == 0) {
            $fail('Vous devez sélectionner au moins un enseignant');
          }
        },
      ],
    ])->validate();

    $activitySession = ActivitySessions::find($activity_session_id);

    $data = ['updated_at' => now()];
    ActivitySessions::whereDate('date', $activitySession->date)->where('establishment_id', $activitySession->establishment_id)->get()->each(function ($session) use ($data) {
      $session->coachs()->syncWithPivotValues(request()->coachs_id, $data);
    });

    $plural = count($request->coachs_id) > 1 ? 's' : '';
    session()->flash('success', "Enseignant{$plural} assigné{$plural}");
    return back();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Establishment $establishment, $id)
  {
    $activitySession = ActivitySessions::withCount('participants')->find($id);
    if ($activitySession->participants_count) {
      session()->flash('warning', "Vous ne pouvez plus supprimer cette séance, $activitySession->participants_count client(s) inscrit(s)");
    } else {
      $activitySession->planning()->decrement('number_activity_sessions', 1);
      session()->flash('success', 'Séance ' . strtoupper($activitySession->activity()->first()->name) . ' supprimée');
      $activitySession->delete();
    }
    return back();
  }
}
