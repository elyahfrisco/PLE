<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Season;
use App\Models\Planning;
use Illuminate\Http\Request;
use App\Models\Establishment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlanningRequest;
use App\Models\ActivitySessions;
use App\Models\Closing;

class PlanningController extends Controller
{

    protected $days = [
        ['en' => 'monday', 'fr' => 'lundi'],
        ['en' => 'tuesday', 'fr' => 'mardi'],
        ['en' => 'wednesday', 'fr' => 'mercredi'],
        ['en' => 'thursday', 'fr' => 'jeudi'],
        ['en' => 'friday', 'fr' => 'vendredi'],
        ['en' => 'saturday', 'fr' => 'samedi'],
        ['en' => 'sunday', 'fr' => 'dimanche']
    ];

    protected $daysArrayKey = ['monday' => [], 'tuesday' => [], 'wednesday' => [], 'thursday' => [], 'friday' => [], 'saturday' => [], 'sunday' => []];

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Establishment $establishment)
    {
        $establishment->load('activities');
        $planningsQuery = $establishment;

        if ($request->season_id) {
            $planningsQuery = $planningsQuery->plannings()->where('season_id', $request->season_id)->orderBy('time_start')->with('activity');
        } else {
            $planningsQuery = $planningsQuery->planningsLastSeason()->with('activity');
        }

        $plannings = $planningsQuery->get();

        $seasons = $establishment->SeasonDesc()->get();

        $activities = $establishment->activities;

        $daily_activities = $this->preparDailyPlanningData($plannings);

        $data = compact('seasons', 'daily_activities', 'establishment', 'activities');

        $data = array_merge($data, request()->all());

        return Inertia::render('Admin/Establishment/Planning/index', $data);
    }

    /**
     * Return a listing of filtered resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request, Establishment $establishment)
    {
        $plannings = $establishment
            ->planningsFiltered($request)
            ->with('activity')
            ->orderAsc()
            ->get();

        $daily_activities = $this->preparDailyPlanningData($plannings);

        return response()->json($daily_activities);
    }

    public function preparDailyPlanningData($plannings)
    {

        $daily_activities = $this->daysArrayKey;

        foreach ($plannings as $key => $planning) {
            $daily_activities[$planning->day][] = $planning;
        }

        return $daily_activities;
    }

    public function _times(Request $request)
    {
        $times = Planning::where('day', $request->day)
            ->where(function ($q) {
                $q->where('start_at', '>=', now())
                    ->orWhere('end_at', '>=', now());
            })
            ->groupBy(DB::raw('time_start, time_end'))
            ->orderBy('time_start');
        if ($request->establishment_id) {
            $times->where('establishment_id', $request->establishment_id);
        }
        $times = $times->get();
        return response(['times' => $times]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Establishment $establishment)
    {
        $establishment->load('activities');

        $seasons = $establishment->seasons()->orderByDesc('year_end')->get();
        $activities = $establishment->activities;
        $days = $this->days;

        return Inertia::render('Admin/Establishment/Planning/create', compact('establishment', 'seasons', 'activities', 'days'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PlanningRequest $request, Establishment $establishment)
    {
        $planningData = $request->all();

        // if (!$request->season_id) {
        //     $planningData['season_id'] = $establishment->lastSeasonId();
        // }

        $planning = $establishment->plannings()->create($planningData);

        $this->calc_number_week($planning);

        if ($request->calculCalendar) {
            $this->_organize_activity_sessions($planning->id);
        }

        return back()->with('success', "Planning créé");
    }

    public function is_closed($date)
    {
        return Closing::where('date_start', $date)->orwhere('date_end', $date)->orWhere(function ($query) use ($date) {
            $query->where('date_start', '<', $date)->where('date_end', '>', $date);
        })->first() ? true : false;
    }

    public function calc_number_week($planning)
    {
        $date = $planning->start_at;
        $session = 0;

        while ($date <= $planning->end_at) {

            if ($this->is_closed($date)) {
                $date->addWeek();
                continue;
            } else {
                $session++;
                $date->addWeek();
            }
        }

        $planning->number_activity_sessions = $session;
        $planning->save();
    }

    public function organize_activity_sessions($planning_id)
    {
        $this->_organize_activity_sessions($planning_id);
        return back()->with('success', "Plannings organisés, les clients peuvent les voir à present");
    }

    public function organize_all_activities_sessions(Establishment $establishment)
    {
        $this->_organize_activity_sessions($establishment->id, true);
        return back()->with('success', "Plannings organisés, les clients peuvent les voir à present");
    }

    public function _organize_activity_sessions($planning_id, $planning_establishment = false)
    {
        if ($planning_establishment) {
            $plannings = Planning::where('establishment_id', $planning_id)->get();
        } else {
            $plannings = Planning::where('id', $planning_id)->get();
        }

        foreach ($plannings as $key => $planning) {

            $sessions = [];

            $activitySessions = $planning->activitySessions()->where('planning_id', $planning->id)->where('accomplished', 0);
            $activitySessions->delete();

            $date = $planning->start_at;

            for ($i = 0; $i < $planning->number_activity_sessions; $i++) {
                if ($this->is_closed($date)) {
                    $date->addWeek();
                    $i--;
                    continue;
                }

                $date_format = $date->format('Y-m-d');
                $time_start = $date_format . ' ' . $planning->time_start->format('H:i:s');
                $time_end = $date_format . ' ' . $planning->time_end->format('H:i:s');

                $sessions[] = (new ActivitySessions([
                    'date' => (clone $date),
                    'time_start' => $time_start,
                    'time_end' => $time_end,
                    'max_effective' => $planning->max_effective,
                    'establishment_id' => $planning->establishment_id,
                    'season_id' => $planning->season_id,
                    'planning_id' => $planning->id,
                    'activity_id' => $planning->activity_id,
                    'hide_to_customer' => $planning->hide_to_customer,
                ]));

                $date->addWeek();
            }

            $planning->activitySessions()->saveMany($sessions);
            $planning->organized = true;
            $planning->save();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Establishment $establishment, $planning_id)
    {

        $establishment->load('activities');

        $planning = Planning::with('activity')->findOrFail($planning_id);

        $seasons = $establishment->seasons()->get();
        $activities = $establishment->activities;
        $days = $this->days;

        $activitySessions_participants_count = $planning->activitySessions()->where('planning_id', $planning_id)->withCount('participants')->pluck('participants_count')->sum();

        return Inertia::render('Admin/Establishment/Planning/edit', compact('planning', 'establishment', 'seasons', 'activities', 'days', 'activitySessions_participants_count'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PlanningRequest $request, Establishment $establishment, $planning_id)
    {
        $planning = Planning::findOrFail($planning_id);

        $data = $request->except(['day', 'activity_id', 'season_id']);

        $visibility_changed = $request->hide_to_customer != $planning->hide_to_customer;

        $planning->update($data);

        $this->calc_number_week($planning);

        $activitySessions_participants_count_query = $planning->activitySessions()->where('planning_id', $planning_id);

        $activitySessions_participants_count = $activitySessions_participants_count_query->withCount('participants')->pluck('participants_count')->sum();

        if ($request->calculCalendar) {

            if ($visibility_changed) {
                (clone $activitySessions_participants_count_query)->update(['hide_to_customer' => $planning->hide_to_customer]);
                session()->flash('info', "la visibilité de la planning a été modifiée");
            }

            /** verifier s'il y deja des souscription sur un des seance de la planning */
            if ($activitySessions_participants_count) {
                return back()->with('error', "Vous ne pouvez plus modifier et régénérer, $activitySessions_participants_count clients sont déjà souscrits");
            } else {
                $planning->update($request->only(['day', 'activity_id', 'season_id']));
                $this->_organize_activity_sessions($planning->id);
            }
        } else {
            foreach ($planning->getChanges() as $key_changed => $value) {
                switch ($key_changed) {
                    case 'max_effective':
                        $planning->activitySessions()->update(['max_effective' => $value]);
                        break;
                    case 'super_pass':
                        $planning->activitySessions()->update(['super_pass' => $value]);
                        break;
                    case 'hide_to_customer':
                        $planning->activitySessions()->update(['hide_to_customer' => $value]);
                        break;
                    case 'time_start':
                        $planning->activitySessions()->get()->each(function ($session) use ($value) {
                            if ("$session->date $value:00" != "$session->time_start") {
                                $session->update([
                                    'time_start' => "$session->date $value:00",
                                ]);
                            }
                        });
                        break;
                    case 'time_end':
                        $planning->activitySessions()->get()->each(function ($session) use ($value) {
                            if ("$session->date $value:00" != "$session->time_end") {
                                $session->update([
                                    'time_end' => "$session->date $value:00",
                                ]);
                            }
                        });
                        break;
                }
            }

            if (!$activitySessions_participants_count) {
                $planning->update($request->only(['day', 'activity_id', 'season_id']));

                foreach ($planning->getChanges() as $key_changed => $value) {
                    switch ($key_changed) {
                        case 'activity_id':
                            $planning->activitySessions()->update(['activity_id' => $value]);
                            break;
                    }
                }
            }
        }

        return back()->with('success', 'Planning modifié');
    }

    /**
     * Stop the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function stop(Establishment $establishment, $planning_id)
    {
        $planning = Planning::findOrFail($planning_id);

        $planning->update(['finished_at' => now()]);
        return redirect()
            ->route('establishments.plannings.index', $establishment->id)
            ->with('success', 'Planning arreté');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Establishment $establishment, $planning_id)
    {
        $planning = Planning::find($planning_id);
        $planning->activitySessions()->delete();
        $planning->delete();

        session()->flash('success', 'Planning supprimée');
        return redirect()->route('establishments.plannings.index', $establishment->id);
    }
}
