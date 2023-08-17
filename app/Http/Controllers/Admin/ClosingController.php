<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Closing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClosingRequest;
use App\Models\Establishment;
use App\Models\Season;
use App\Models\Trimester;
use PHPUnit\Framework\Constraint\IsNull;

class ClosingController extends Controller
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
  public function index(Request $request)
  {

    $trimester_id = $request->trimester_id;
    $season_id = $request->season_id;
    $establishment_id = $request->establishment_id;

    $closings = Closing::filter($request)->SelectDaysClosingCount()->get();

    $trimester = $trimester_id ? Trimester::find($trimester_id) : null;
    $season = $season_id ? Season::find($season_id) : null;
    $establishment = $establishment_id ? Establishment::find($establishment_id) : null;

    return Inertia::render('Admin/Closing/index', compact('closings', 'establishment', 'season', 'trimester'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(ClosingRequest $request)
  {
    $dataClosing = $request->except('oneDay');

    if ($request->oneDay || is_null($request->date_end)) {
      $dataClosing['date_end'] = $dataClosing['date_start'];
    }

    Closing::create($dataClosing);

    return back()->with('success', 'fermeture ajoutée');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Closing
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Closing
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Closing
   * @return \Illuminate\Http\Response
   */
  public function update(ClosingRequest $request, $id)
  {
    $dataClosing = $request->except('oneDay');

    if ($request->oneDay) {
      $dataClosing['date_end'] = $dataClosing['date_start'];
    }

    Closing::find($id)->update($dataClosing);
    return back()->with('success', 'fermeture modifié');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Closing
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    Closing::destroy($id);
    return back()->with('success', 'Saison supprimée');
  }
}
