<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Season;
use Illuminate\Http\Request;
use App\Models\Establishment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\EstablishmentRequest;

class EstablishmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $establishments = Cache::remember('establishments_list', 60, function () {
            return Establishment::all();
        });
        return Inertia::render('Admin/Establishment/index', compact('establishments'));
    }

    /**
     * Display a listing of establishment activities .
     *
     * @return \Illuminate\Http\Response
     */
    public function establishment_activities(Establishment $establishment)
    {
        $establishment->loadCount('activities');
        $activities = $establishment->activities()->get();

        $activitiesNotAttached = $establishment->activitiesNotAttached()->get();

        return Inertia::render('Admin/Establishment/Activity/index', compact('activities', 'establishment', 'activitiesNotAttached'));
    }

    /**
     * Attach establishment activities .
     *
     * @return \Illuminate\Http\Response
     */
    public function attachActivity(Establishment $establishment, $activity_id)
    {
        $establishment->activities()->attach($activity_id);
        forget_cache("establishment_{$establishment->id}_activities");
        session()->flash('success', "Activité attachée à l'établissement");
        return back();
    }

    /**
     * Detach establishment activities .
     *
     * @return \Illuminate\Http\Response
     */
    public function detachActivity(Establishment $establishment, $activity_id)
    {
        $establishment->activities()->detach($activity_id);
        forget_cache("establishment_{$establishment->id}_activities");
        session()->flash('success', "Activité detachée de l'établissement");
        return back();
    }

    public function toggle_out_pass_activity(Establishment $establishment, $activity_id)
    {
        $r = DB::table('establishment_activity')->where(['activity_id' => $activity_id, 'establishment' => $establishment->id]);
        $r->update([
            'out_pass' => DB::raw('!out_pass')
        ]);
        $statut = $r->first()->out_pass;
        return redirect()
            ->route('establishments.activities.index', $establishment->id)
            ->with('success', "L'activité " . ($statut ? 'doit être souscrit avec' : 'peut être souscrit sans') . " Pass de l'établissement");
    }

    /**
     * Display a listing of establishment activities .
     *
     * @return \Illuminate\Http\Response
     */
    public function establishmentPasses(Establishment $establishment)
    {
        $establishment->loadCount('passes');
        $passes = $establishment->passes()->get();
        $passesNotAttached = $establishment->passesNotAttached()->get();

        return Inertia::render('Admin/Establishment/Pass/index', compact('passes', 'establishment', 'passesNotAttached'));
    }

    /**
     * Attach establishment activities .
     *
     * @return \Illuminate\Http\Response
     */
    public function attach_pass(Establishment $establishment, $pass_id)
    {
        $establishment->passes()->attach($pass_id);
        forget_cache('establishment_pass_' . $establishment->id);
        session()->flash('success', "Pass attaché à l'établissement");
        return back();
    }


    /**
     * Display a listing of establishment activities .
     *
     * @return \Illuminate\Http\Response
     */
    public function detach_pass(Establishment $establishment, $pass_id)
    {
        $establishment->passes()->detach($pass_id);
        forget_cache('establishment_pass_' . $establishment->id);
        session()->flash('success', "Pass detaché de l'établissement");
        return back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Admin/Establishment/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EstablishmentRequest $request)
    {
        Establishment::create($request->all());

        session()->forget('establishments_list');
        Cache::forget('establishments_list');

        session()->flash('success', 'Etablissement ajouté');
        return redirect()->route('establishments.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Establishment $establishment)
    {
        $establishment->load('seasons');
        return $establishment;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Establishment $establishment)
    {
        return Inertia::render('Admin/Establishment/edit', compact('establishment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EstablishmentRequest $request, Establishment $establishment)
    {
        $establishment->update($request->all());

        session()->forget('establishments_list');
        Cache::forget('establishments_list');

        session()->flash('success', 'Etablissement modifié');
        return redirect()->route('establishments.edit', $establishment->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Establishment $establishment)
    {
        $establishment->delete();
        session()->flash('success', 'Etablissement supprimé');
        return redirect()->route('establishments.index');
    }
}
