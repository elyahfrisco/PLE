<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EstablishmentApiController extends Controller
{

  public function index()
  {
    return Establishment::all();
  }

  public function seasons(Establishment $establishment)
  {
    return
      Cache::remember(
        "establishment_{$establishment->id}_seasons",
        60 * 60 * 24, /* 24h */
        fn () =>
        $establishment->seasons()->get()
      );
  }

  public function unfinishedSeasons(Establishment $establishment)
  {
    return
      Cache::remember(
        "establishment_{$establishment->id}_seasons_unfinished",
        60 * 60 * 24, /* 24h */
        fn () =>
        $establishment->seasons()
          ->unfinished()
          ->get()
      );
  }

  public function passes(Request $request, Establishment $establishment)
  {
    if (is_numeric($request->season_id)) {
      $QueryEstablishmentPasses = Season::find($request->season_id)->passes();
    } else {
      $QueryEstablishmentPasses = $establishment->passes();
    }
    return $QueryEstablishmentPasses->get();
  }

  public function activities(Establishment $establishment)
  {
    return Cache::remember(
      "establishment_{$establishment->id}_activities",
      60 * 60 * 24, /* 24h */
      fn () =>  $establishment->activities()->get()
    );
  }
}
