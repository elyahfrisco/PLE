<?php

namespace App\Http\Controllers;

use App\Models\Pass;
use Inertia\Inertia;
use App\Models\Price;
use App\Models\Season;
use Illuminate\Http\Request;
use App\Http\Requests\PriceRequest;

class PassPriceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($establishment_id, $season_id, $pass_id)
  {
    request()->request->set('getPrices', compact(
      'establishment_id',
      'season_id',
      'pass_id',
    ));

    $season = Season::with('establishment')
      ->where('establishment_id', $establishment_id)
      ->where('id', $season_id)->firstOrFail();

    $pass = Pass::findOrFail($pass_id);

    $price = Price::relatedTables()->where('establishment_id', $establishment_id)->where('pass_id', $pass_id)->first();
    return Inertia::render('Admin/Season/Pass/Price/index', compact('price', 'season', 'pass'));
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
  public function store(PriceRequest $request, $establishment_id, $season_id, $pass_id)
  {

    $price = [
      [
        'pass_id' => $pass_id,
        'season_id' => $season_id,
        'establishment_id' => $establishment_id,
      ],
      [
        'reduced_price' => $request->reduced_price,
        'price' => $request->price,
      ]
    ];

    Price::updateOrCreate(
      $price[0],
      $price[1]
    );

    $pass = Pass::find($pass_id);

    return back()->with('success', 'Prix ' . $pass->name . ' à jour');
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
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
