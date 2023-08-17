<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivitySessionItemResource;
use App\Models\ActivitySessions;
use App\Models\Establishment;
use App\Models\Repositories\ActivitySessionRepository;
use App\Models\Repositories\SessionsRepository;
use App\Models\Trimester;
use Illuminate\Http\Request;

class ActivitySessionApiController extends Controller
{
  public $activitySessionRepository;
  public $sessionsRepository;

  public function __construct(ActivitySessionRepository $activitySessionRepository)
  {
    $this->activitySessionRepository = $activitySessionRepository;
    $this->sessionsRepository = new SessionsRepository();
  }

  public function activitySessionsByEstablishment(Request $request, $establishment_id)
  {
    $planningSession = $this
      ->activitySessionRepository
      ->getFilteredActivitySessionsFor($request, $establishment_id);

    $planningSession = ActivitySessionItemResource::collection($planningSession);

    return response()->json($planningSession);
  }

  public function planningSessions(Request $request)
  {
    $sessions = $this
      ->sessionsRepository
      ->getFilteredSessionsFor($request);
    
    return ActivitySessionItemResource::collection($sessions);
  }

  public function trimesterOfPlanningSession(Request $request)
  {
    if (!$request->activity_session_id) return 0;

    $session = ActivitySessions::joinSeason()
      ->selectRaw('plannings.season_id, activity_sessions.date')
      ->find($request->activity_session_id);

    return Trimester::where('season_id', $session->season_id)
      ->where('date_end', '>=', $session->date)
      ->where('date_start', '<=', $session->date)
      ->first()->num_trimester ?? Trimester::where('season_id', $session->season_id)->orderByDesc('num_trimester')->first()->num_trimester ?? 0;
  }

  public function activitySessionsParticipants(Establishment $establishment, ActivitySessions $activity_session)
  {
    $participants = $this
      ->activitySessionRepository
      ->getParticipantsOf($activity_session);

    return response()->json($participants);
  }
}
