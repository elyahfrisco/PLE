<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Price;
use App\Models\Season;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\Establishment;
use App\Http\Requests\PriceRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveManagementPriceRequest;
use App\Http\Requests\Admin\SaveRegistrationPriceRequest;
use App\Models\Trimester;

class PriceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Establishment $establishment, $season_id, $activity_id)
  {
    request()->request->set(
      'getPrices',
      [
        'establishment_id' => $establishment->id,
        'season_id' => $season_id,
        'activity_id' => $activity_id
      ]
    );

    $season = Season::with('establishment')
      ->with('trimesters')
      ->where('establishment_id', $establishment->id)
      ->where('id', $season_id)->firstOrFail();

    $activity = Activity::findOrFail($activity_id);

    $passes_one_session = Season::find($season_id)->passesOneSession()->get();

    $prices = Price::relatedTables()
      ->where('prices.establishment_id', $establishment->id)
      ->where('prices.season_id', $season_id)
      ->where('prices.activity_id', $activity_id)
      ->get();

    return Inertia::render('Admin/Season/Activity/Price/index', compact('prices', 'season', 'activity', 'passes_one_session'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(PriceRequest $request, Establishment $establishment, $season_id, $activity_id)
  {

    $prices_trimester = [];

    if ($request->trimesters) {
      foreach ($request->trimesters as $key => $trimester) {

        $trimesters_ = [$trimester];

        $otherSameNumberTrimester = Trimester::where('season_id', $trimester['season_id'])
          ->where('id', '<>', $trimester['id'])
          ->where('num_trimester', $trimester['num_trimester'])->get();

        $trimesters_ = array_merge($trimesters_, $otherSameNumberTrimester->toArray());

        foreach ($trimesters_ as $key => $trimester_) {

          $prices_trimester[] = [
            [
              'season_id' => $season_id,
              'trimester_id' => $trimester_['id'],
              'activity_id' => $activity_id,
              'establishment_id' => $establishment->id,
              'type' => 'trimester',
            ],
            [
              'reduced_price' => ($trimester['reduced_price'] ?? null),
              'reduced_price2' => ($trimester['reduced_price2'] ?? null),
              'price' => ($trimester['price'] ?? 0) . '',
            ]
          ];
        }
      }
    }

    $prices_pass = [];

    if ($request->passes) {
      foreach ($request->passes as $key => $pass) {
        $prices_pass[] = [
          [
            'season_id' => $season_id,
            'pass_id' => $pass['id'],
            'activity_id' => $activity_id,
            'establishment_id' => $establishment->id,
            'type' => 'pass',
          ],
          [
            'reduced_price' => ($pass['reduced_price'] ?? null),
            'reduced_price2' => ($pass['reduced_price2'] ?? null),
            'price' => ($pass['price'] ?? 0) . '',
          ]
        ];
      }
    }

    foreach (array_merge($prices_trimester, $prices_pass) as $key => $price) {
      Price::updateOrCreate(
        $price[0],
        $price[1]
      );
    }

    return back()->with('success', 'Prix activité à jour');
  }

  public function storeRegistrationPrice(SaveRegistrationPriceRequest $request, Establishment $establishment, $season_id)
  {
    if ($request->category == 'adult') {
      $price = $request->price_adult;
      $reduced_price = $request->reduced_price_adult;
      session()->flash('success', 'Frais d\'inscription adulte modifié');
    } else if ($request->category == 'child') {
      $price = $request->price_child;
      $reduced_price = $request->reduced_price_child;
      session()->flash('success', 'Frais d\'inscription enfant modifié');
    } else {
      return back();
    }

    Price::updateOrCreate(
      [
        'season_id' => $season_id,
        'category' => $request->category,
        'type' => 'registration',
      ],
      [
        'reduced_price' => $reduced_price,
        'price' => $price,
      ]
    );

    return back();
  }

  public function storeManagementPrice(SaveManagementPriceRequest $request, Establishment $establishment, $season_id)
  {
    if ($request->category == 'adult') {
      $price = $request->price_adult;
      $reduced_price = $request->reduced_price_adult;
      session()->flash('success', 'Frais de gestion adulte modifié');
    } else if ($request->category == 'child') {
      $price = $request->price_child;
      $reduced_price = $request->reduced_price_child;
      session()->flash('success', 'Frais de gestion enfant modifié');
    } else {
      abort(403);
    }

    Price::updateOrCreate(
      [
        'season_id' => $season_id,
        'category' => $request->category,
        'type' => 'management',
      ],
      [
        'reduced_price' => $reduced_price,
        'price' => $price,
      ]
    );
    return back();
  }
}
