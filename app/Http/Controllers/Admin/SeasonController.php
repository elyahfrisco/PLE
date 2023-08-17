<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pass;
use Inertia\Inertia;
use App\Models\Season;
use App\Models\Planning;
use Illuminate\Http\Request;
use App\Models\Establishment;
use App\Models\ActivitySessions;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeasonRequest;
use function GuzzleHttp\Promise\each;
use Illuminate\Support\Facades\Cache;

class SeasonController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Establishment $establishment)
  {
    $seasons = Season::status()
      ->where('establishment_id', $establishment->id)
      ->with(['management_price', 'registration_price'])
      ->orderByDesc('year_end')
      ->get();
    return Inertia::render('Admin/Season/index', compact('seasons', 'establishment'));
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Establishment $establishment)
  {
    return Inertia::render('Admin/Season/create', compact('establishment'));
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function seasonPasses(Establishment $establishment, $season_id)
  {
    $season = Season::withCount('passes')->with('establishment')->findOrFail($season_id);

    $passes = $season->passes()->withCount(['activities'])->get();
    $passes_id = $passes->pluck('id');
    $passesNotAttached = $season->passesNotAttached($passes_id)->get();

    return Inertia::render("Admin/Season/Pass/index", compact('season', 'passes', 'passesNotAttached'));
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function seasonActivities(Establishment $establishment, $season_id)
  {
    $season = Season::withCount('passes')->with('establishment')->findOrFail($season_id);

    $activities = Season::activities()->where('seasons.id', $season_id)->get();
    $activitiesNotAttached = $season->activitiesNotAttached()->get();

    return Inertia::render("Admin/Season/Activity/index", compact('establishment', 'season', 'activities', 'activitiesNotAttached'));
  }

  /**
   * Attach the season pass .
   *
   * @return \Illuminate\Http\Response
   */
  public function attachPass($season_id, $pass_id)
  {
    $season = Season::find($season_id);
    $season->passes()->attach($pass_id, ['establishment_id' => $season->establishment_id]);
    session()->flash('success', "le Pass est attaché de la saison");
    return back();
  }

  /**
   * Detach the season pass .
   *
   * @return \Illuminate\Http\Response
   */
  public function detachPass($season_id, $pass_id)
  {
    $activities = Season::find($season_id)->passes();
    $activities->detach($pass_id);
    session()->flash('success', "le Pass est detaché de la saison");
    return back();
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(SeasonRequest $request, Establishment $establishment)
  {

    $season_preced = $establishment
      ->seasons()
      ->orderByDesc('id')
      ->with('passes')
      ->whereHas('passes')
      ->first();

    $season = $establishment->seasons()->create($request->all());


    if ($season_preced && $request->copy_preced_season_parameters) {
      $passe_to_attach = [];

      foreach ($season_preced->passes as $key => $passes) {
        $passe_to_attach[$passes['id'] . ''] = ['establishment_id' => $season->establishment_id];
      }

      $season->passes()->attach($passe_to_attach);
      session()->flash('info', 'Les paramètres de la saison précédente ont été copiés et attribués à la saison ' . $season->year_start . ' - ' . $season->year_end);
    }

    session()->flash('success', 'Saison ajouté');
    return redirect()->route('establishments.seasons.index', $establishment->id);
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
  public function edit($season_id)
  {
    $season = Season::whereId($season_id)->with('establishment')->firstOrFail();

    return Inertia::render('Admin/Season/edit', compact('season'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(SeasonRequest $request, $id)
  {
    Season::find($id)->update($request->all());
    session()->flash('success', 'Saison modifié');
    return redirect()->route('establishments.seasons.index', $request->establishment_id);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {

    $season = Season::find($id);
    $establishment_id = $season->establishment_id;

    $season->trimesters()->delete();

    $season->passes()->detach();

    ActivitySessions::whereIn('planning_id', $season->plannings()->select('id'))->delete();

    $season->plannings()->forceDelete();

    $season->delete();

    session()->flash('success', 'Saison supprimée');
    return redirect()->route('establishments.seasons.index', $establishment_id);
  }
}
