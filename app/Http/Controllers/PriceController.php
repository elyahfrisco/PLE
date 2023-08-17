<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceRequest;
use Inertia\Inertia;
use App\Models\Price;
use App\Models\Season;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\Establishment;

class PriceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($establishment_id, $season_id, $activity_id)
  {
    request()->request->set('getPrices', compact(
      'establishment_id',
      'season_id',
      'activity_id',
    ));

    $season = Season::with('establishment')
      ->with('trimesters')
      ->where('establishment_id', $establishment_id)
      ->where('id', $season_id)->firstOrFail();

    $activity = Activity::findOrFail($activity_id);

    $passes_one_session = Season::find($season_id)->passesOneSession()->get();

    $prices = Price::relatedTables()->where('establishment_id', $establishment_id)->where('activity_id', $activity_id)->get();
    return Inertia::render('Admin/Season/Activity/Price/index', compact('prices', 'season', 'activity', 'passes_one_session'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(PriceRequest $request, $establishment_id, $season_id, $activity_id)
  {
    $prices_trimester = [];
    foreach ($request->trimesters as $key => $trimester) {
      $prices_trimester[] = [
        [
          'trimester_id' => $trimester['id'],
          'activity_id' => $activity_id,
          'establishment_id' => $establishment_id,
        ],
        [
          'reduced_price' => ($trimester['reduced_price'] ?? null),
          'price' => ($trimester['price'] ?? 0) . '',
        ]
      ];
    }

    $prices_pass = [];
    foreach ($request->passes as $key => $pass) {
      $prices_pass[] = [
        [
          'pass_id' => $pass['id'],
          'activity_id' => $activity_id,
          'establishment_id' => $establishment_id,
        ],
        [
          'reduced_price' => ($pass['reduced_price'] ?? null),
          'price' => ($pass['price'] ?? 0) . '',
        ]
      ];
    }

    foreach (array_merge($prices_trimester, $prices_pass) as $key => $price) {
      Price::updateOrCreate(
        $price[0],
        $price[1]
      );
    }

    return back()->with('success', 'Prix activité à jour');
  }
}
