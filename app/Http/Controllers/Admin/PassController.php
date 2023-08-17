<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pass;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\PassRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class PassController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $passes = Cache::remember('passes_list', 60 * 60 * 12 /* hours */, function () {
            return Pass::orderBy('name')->with('category')->get();
        });
        return renderJsonOrInertia("Admin/Pass/index", compact('passes'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activitiesPass($pass_id)
    {
        $pass = Pass::with('category')->findOrFail($pass_id);

        $activities = $pass->activities()->with('activity_group')->get();
        $activitiesNotAttached = $pass->activitiesNotAttached()->get();
        $activities_groups = $pass->activity_groups()->get();

        return Inertia::render("Admin/Pass/Activity/index", compact('pass', 'activities', 'activitiesNotAttached', 'activities_groups'));
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function detachActivity($pass_id, $activity_id)
    {
        $activities = Pass::find($pass_id)->activities();
        $activities->detach($activity_id);

        forget_cache("pass_{$pass_id}_activities");

        session()->flash('success', "Activité detaché du Pass");
        return back();
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function attachActivity($pass_id, $activity_id)
    {
        $activities = Pass::find($pass_id)->activities();
        $activities->attach($activity_id);

        forget_cache("pass_{$pass_id}_activities");

        session()->flash('success', "Activité attaché au Pass");
        return back();
    }

    public function moveGroupActivity(Request $request, $pass_id, $acticity_id)
    {
        dd($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render("Admin/Pass/create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PassRequest $request)
    {
        Pass::create($request->all());

        forget_cache('passes_list');

        session()->flash('success', 'Pass ajouté');
        return redirect()->route('passes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pass = Pass::find($id);
        return Inertia::render("Admin/Pass/edit", compact('pass'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PassRequest $request, $id)
    {
        $pass = Pass::find($id);
        $pass->update($request->all());

        forget_cache('passes_list');
        forget_cache("pass_{$pass->id}_activities");

        session()->flash('success', 'Pass modifié');
        return redirect()->route('passes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Pass::destroy($id);

        forget_cache('passes_list');

        session()->flash('success', 'Pass supprimé');
        return back();
    }
}
