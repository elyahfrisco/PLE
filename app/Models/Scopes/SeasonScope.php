<?php

namespace App\Models\Scopes;

use App\Models\Activity;
use App\Models\Pass;
use Illuminate\Support\Facades\DB;

trait SeasonScope
{
  public function scopeUnfinished($query)
  {
    $query->whereDate('seasons.date_end', '>=', now());
  }

  public function scopeActivities()
  {
    return $this
      ->queryActivities();
  }

  public function scopeStatus()
  {
    return $this
      ->select(DB::raw('*, IF(date_end < NOW(), "termined", IF(date_end > NOW() AND date_start < NOW(), "progress", "future" )) as status'));
  }

  public function scopeActivitiesOneSession()
  {
    return $this
      ->queryActivities()
      ->where('activities.one_session', 1);
  }

  public function passesNotAttached($passes_id)
  {
    return
      Pass::whereNotIn(
        'id',
        $passes_id,
      )
      ->whereIn(
        'id',
        $this
          ->establishment()->first()
          ->passes()
          ->pluck('id'),
      )
      ->orderBy('name');
  }

  protected function queryActivities()
  {
    return $this
      ->leftJoin('season_pass', "season_pass.season_id", "=", 'seasons.id')
      ->leftJoin('pass_activity', "pass_activity.pass_id", "=", 'season_pass.pass_id')
      ->rightJoin('activities', "activities.id", "=", 'pass_activity.activity_id')
      ->select(DB::raw('activities.*, seasons.id as season_id'))
      ->groupBy('activities.id')
      ->orderBy('activities.name');
  }

  public function activitiesNotAttached()
  {
    return Activity::whereNotIn(
      'id',
      (clone $this)
        ->leftJoin('season_pass', "season_pass.season_id", "=", 'seasons.id')
        ->leftJoin('pass_activity', "pass_activity.pass_id", "=", 'season_pass.pass_id')
        ->rightJoin('activities', "activities.id", "=", 'pass_activity.activity_id')
        ->select(DB::raw('DISTINCT(activities.id), activities.*'))->pluck('activities.id')
    )->orderBy('name');
  }
}
