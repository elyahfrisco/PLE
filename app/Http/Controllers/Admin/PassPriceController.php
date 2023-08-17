<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pass;
use Inertia\Inertia;
use App\Models\Price;
use App\Models\Season;
use App\Http\Requests\PriceRequest;
use App\Http\Controllers\Controller;
use App\Models\Establishment;

class PassPriceController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Establishment $establishment, $season_id, $pass_id)
  {
    request()->request->set(
      'getPrices',
      [
        'establishment_id' => $establishment->id,
        'season_id' => $season_id,
        'pass_id' => $pass_id,
      ]
    );

    $season = Season::with('establishment')
      ->where('establishment_id', $establishment->id)
      ->where('id', $season_id)->firstOrFail();

    $pass = Pass::findOrFail($pass_id);

    $price = Price::relatedTables()->where('establishment_id', $establishment->id)->where('prices.season_id', $season_id)->where('pass_id', $pass_id)->first();
    return Inertia::render('Admin/Season/Pass/Price/index', compact('price', 'season', 'pass'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(PriceRequest $request, Establishment $establishment, $season_id, $pass_id)
  {
    $price = [
      [
        'pass_id' => $pass_id,
        'season_id' => $season_id,
        'establishment_id' => $establishment->id,
        'type' => 'pass',
      ],
      [
        'reduced_price' => $request->reduced_price,
        'price' => $request->price,
      ]
    ];

    $price = Price::updateOrCreate(
      $price[0],
      $price[1]
    );

    $pass = Pass::find($pass_id);
    session()->flash('success', 'le prix du ' . $pass->name . ' de la saison est à jour');
    return back();
  }
}
