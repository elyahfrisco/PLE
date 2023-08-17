<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use App\Models\Price;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SeasonApiController extends Controller
{
  public function fees(Request $request, $season_id)
  {
    $cache_name = array_merge([
      'type' => 'season_fees',
      'season_id' => $season_id,

    ], $request->all());

    return Cache::remember(
      to_cache_name($cache_name),
      60 * 60 * 24, /* 24h */
      function () use ($request, $season_id) {

        $QueryFees = Price::where('season_id', $season_id)
          ->whereValueNotNullAndEqualTo([
            'type' => $request->type,
            'category' => $request->category
          ]);

        if ($request->first) {
          return $QueryFees->first();
        }

        return $QueryFees->get();
      }
    );
  }

  public function nextSeason($season_id)
  {
    return
      Cache::remember('season_next_' . $season_id, 60, function () use ($season_id) {
        $season = Season::find($season_id);
        $next_season = Season::where('id', '>', $season_id)
          ->where('establishment_id', $season->establishment_id)
          ->whereDiff('year_start', $season->year_start)
          ->first();
        return $next_season->id ?? null;
      });
  }

  public function trimesters(Request $request, Season $season)
  {
    return $season
      ->trimesters()
      ->includeScopeWhereValueNotNull([
        'unfinished' => $request->unfinished,
        'groupByNumTrimester' => $request->group,
      ])->get();
  }

  public function activities($season_id)
  {
    return Season::activities()->where('seasons.id', $season_id)->get();
  }

  public function passesCategories(Request $request, Season $season = null)
  {
    $cache_name = [
      'type' => 'season_passes_categories',
      'season' => $season->id ?? 0,
      'planning_id' => $request->planning_id,
    ];

    return Cache::remember(
      to_cache_name($cache_name),
      60 * 60 * 12, /* 12h */
      function () use ($request, $season) {

        $categories = [
          "trimester" => false,
          "other" => false,
          "oneSession" => false,
          "decouvert" => false,
        ];

        if ($season) {
          $passes = $season->passes()->get();
        } elseif ($request->planning_id) {
          $passes = Planning::find($request->planning_id)->season()->first()->passes()->get();
        } else {
          return $categories;
        }

        foreach ($passes as $pass) {
          if ($pass->pass_trimester) {
            $categories['trimester'] = true;
          } elseif ($pass->name == 'PASS DECOUVERTE') {
            $categories['decouvert'] = true;
          } elseif ($pass->number_sessions > 1 || $pass->number_sessions == null) {
            $categories['other'] = true;
          } elseif (is_numeric($pass->number_sessions) && (int) $pass->number_sessions == 1) {
            $categories['oneSession'] = true;
          }
        }

        return $categories;
      }
    );
  }

  public function passesByPlanning(Request $request)
  {
    $request->validate([
      'planning_id' => 'required|integer',
    ]);

    $season = Planning::find($request->planning_id)->season()->firstOrFail();

    return $this->passesBySeason($request, $season);
  }

  public function passesBySeason(Request $request, Season $season)
  {

    $cache_name = array_merge([
      'type' => 'season_passes',
      'season' => $season->id,
    ], $request->all());

    return Cache::remember(
      to_cache_name($cache_name),
      60 * 60 * 12, 
      /* 12h */
      function () use ($request, $season) {
        $passes = $season
          ->passes()
          ->with('activities', 'activity_groups');

        if ($request->pass_type) {
          switch ($request->pass_type) {
            case 'trimester':
              $passes->passTrimester();
              break;
            case 'decouvert':
              $passes
                ->passTrimester(false)
                ->where('name', 'PASS DECOUVERTE')
                ->where(function ($query) {
                  $query
                    ->where('number_sessions', ">", 1)
                    ->orWhere('number_sessions', "=", null);
                });
              $passes->with('price');
              break;
            case 'other':
              $passes
                ->passTrimester(false)
                ->where('name', '!=', 'PASS DECOUVERTE')
                ->where(function ($query) {
                  $query
                    ->where('number_sessions', ">", 1)
                    ->orWhere('number_sessions', "=", null);
                });
              $passes->with('price');
              break;
            case 'one_session':
              $passes->where('number_sessions', 1);
              break;
          }
        }

        return $passes->get();
      }
    );
  }
}
