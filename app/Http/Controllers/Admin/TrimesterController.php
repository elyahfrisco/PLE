<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Season;
use App\Models\Trimester;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrimesterRequest;
use App\Models\SubscriptionActivity;

class TrimesterController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($season_id)
  {
    $trimesters = Trimester::where('season_id', $season_id)->get();
    $season = Season::find($season_id);

    return Inertia::render("Admin/Trimester/index", compact('trimesters', 'season_id', 'season'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    // return Inertia::render("Admin/Trimester/create", compact('trimesters'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(TrimesterRequest $request, $id)
  {
    $data = $request->all();
    // dd($data);
    if (!$request->week_count) {
      $date_start = Carbon::parse($request->date_start);
      $date_end = Carbon::parse($request->date_end);
      $data['week_count'] = $date_start->diffInWeeks($date_end) + 1;
    }
    Trimester::create($data);
    return redirect()->route('establishments.seasons.trimesters.index', $request->season_id)->with('success', 'Trimestre crée');
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
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(TrimesterRequest $request, $id)
  {

    $data = $request->all();

    $date_start = Carbon::parse($request->date_start);
    $date_end = Carbon::parse($request->date_end);

    $data['week_count'] = $date_start->diffInWeeks($date_end) + 1;

    Trimester::find($id)->update($data);

    session()->flash('success', 'Trimestre modifié');
    return back();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $trimester = (Trimester::find($id));
    $season_id = $trimester->season_id;
    $trimester->delete();
    return redirect()->route('establishments.seasons.trimesters.index', $season_id)->with('success', 'Trimestre supprimée');
  }
}
