<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Closing;
use App\Models\Pass;
use App\Models\Planning;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PlanningApiController extends Controller
{
  public function plannings(Request $request)
  {
    $planningQuery = Planning::select("*")
      ->whereValueNotNullAndEqualTo([
        'activity_id' => $request->activity_id,
        'organized' => $request->organized,
        'establishment_id' => $request->establishment_id,
        'season_id' => $request->season_id,
        'day' => $request->day
      ])
      ->groupByDayIf($request->group_day)
      ->whereDateStartAt($request->date_start)
      ->whereStartAfterNow($request->startOrEndGreatNow)
      ->isActivityForPass($request->pass_id);

    if (is_numeric($request->num_trimester)) {
      $planningQuery->WTrimester($request->season_id, $request->num_trimester);
    }

    return $planningQuery->get();
  }

  public function list(Request $request)
  {
    $planningsQuery = Planning::search()->with('establishment')->orderByRaw('start_at ASC, time_start ASC');

    if (is_numeric($request->establishment_id)) {
      $planningsQuery->where('plannings.establishment_id', $request->establishment_id);
    } else if ($request->season_id) {
      $planningsQuery = $planningsQuery->plannings()->where('season_id', $request->season_id)->orderBy('time_start')->with('activity');
    }

    return response($planningsQuery->get());
  }

  public function planningDisabledDate(Request $request)
  {
    return Cache::remember(
      to_cache_name($request->all()),
      (60 * 60), /* 1h */
      function () {
        $r_closings = Closing::filter(request())->get();

        $closings = [];

        foreach ($r_closings as $key => $closing) {
          $period = CarbonPeriod::create($closing->date_start, $closing->date_end);
          foreach ($period as $date) {
            $closings[] = $date->format('Y-m-d');
          }
        }

        return array_values(array_unique($closings));
      }
    );
  }

  public function seasonOfPlanning(Request $request)
  {
    return Planning::find($request->planning_id)->season()->first();
  }
}
