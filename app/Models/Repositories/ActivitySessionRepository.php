<?php

namespace App\Models\Repositories;

use App\Models\ActivitySessions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ActivitySessionRepository
{
  public function getFilteredActivitySessionsFor(Request $request, $establishment_id)
  {
    if (!in_array($request->view, ['week', 'day'])) {
      return Cache::remember(
        to_cache_name($request->all()),
        60 * 60 * 1, /* 1h */
        fn () => $this->queryFilteredActivitySessionsFor($request, $establishment_id)
      );
    } else {
      return $this->queryFilteredActivitySessionsFor($request, $establishment_id);
    }
  }

  private function queryFilteredActivitySessionsFor(Request $request, $establishment_id)
  {
    $QueryPlanningSession = ActivitySessions::with(['activity'])
      ->JoinAbsences();

    if (!in_array($request->view, ['years', 'year', 'month'])) {
      $QueryPlanningSession->with(['coachs']);
    }

    if ($request->start) {
      $date_start = $request->start;
      $date_end = $request->end;
      $QueryPlanningSession->filter($date_start, $date_end);
    } else if (intval($request->session_id)) {
      $QueryPlanningSession->where('activity_sessions.id', $request->session_id);
    } else {
      $QueryPlanningSession->LastWeekSession();
    }

    /** planning Filter */
    if (is_numeric($request->planning_id)) {
      $QueryPlanningSession
        ->where('activity_sessions.planning_id', intval($request->planning_id));
    }
    /** season Filter */
    elseif (is_numeric($request->season_id)) {
      $QueryPlanningSession
        ->where('activity_sessions.season_id', $request->season_id)
        // ->join('seasons', 'plannings.season_id', '=', "seasons.id")
        // ->whereRaw("(activity_sessions.date >= seasons.date_start AND activity_sessions.date <= seasons.date_end)")
      ;
    } elseif (!intval($request->session_id)) {
      $QueryPlanningSession->where('activity_sessions.establishment_id', $establishment_id);
    }

    /** activity Filter */
    if (!$request->planning_id && is_numeric($request->activity_id)) {
      $QueryPlanningSession
        ->where('activity_sessions.activity_id', $request->activity_id);
    }

    $QueryPlanningSession
      /** trimester num Filter */
      ->when(
        is_numeric($request->num_trimester),
        fn ($query) => $query->WTrimester($request->season_id, $request->num_trimester)
      )
      ->when(
        is_numeric($request->participant_min),
        fn ($query)  =>
        $query->has('participantsNoRelation', '>=', intval($request->participant_min))
      )
      ->when(
        is_numeric($request->participant_max),
        fn ($query)  =>
        $query->has('participantsNoRelation', '>=', intval($request->participant_max))
      );

    $planningSession = $QueryPlanningSession->get();

    $planningSession->loadCount('participants');
    $planningSession->loadCount([
      'participants_pass_trimester' => fn ($query) => $query
        ->where('is_recuperation', false)
        ->join(
          'passes',
          fn ($q) =>
          $q->on("subscription_activities.pass_id", '=', "passes.id")
            ->where("passes.pass_trimester", true)
        )
    ]);

    return $planningSession;
  }

  public function getParticipantsOf($activity_session)
  {
    $activity_session->load(['activity', 'establishment']);
    $participants = $activity_session->participants()->get();

    return $participants->map(function ($participant) {
      $participant->status = '';

      if ($participant->is_recuperation)
        $participant->status = 'En récupération';

      if ($participant->absence_prevention_id)
        $participant->status = 'Absence prévenue';

      switch ($participant->session_status_txt) {
        case 'queued':
          if ($participant->presence_confirmed_at) {
            $participant->session_status_txt_fr = "Présence confirmée sur la séance de rattrapage";
          } else {
            $participant->session_status_txt_fr = "file d'attente";
          }
          break;
        case 'debited':
          $participant->session_status_txt_fr = "debité";
          break;
      }

      return $participant;
    });
  }
}
