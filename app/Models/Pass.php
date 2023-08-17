<?php

namespace App\Models;

use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Pass extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'name',
    'pass_num',
    'number_sessions',
    'period_validity',
    'period_validity_unit',
    'pass_category_id',
    'one_session',
    'price_per_person',
    'pass_category_id',
    'pass_trimester',
    'care',
  ];

  protected $appends = ['PassCategory'];

  public function scopefindInCache($query, $pass_id)
  {
    return Cache::remember(
      "pass_$pass_id",
      60 * 60 * 12,
      fn () =>
      $query->find($pass_id)
    );
  }

  public function scopeOneSession($query)
  {
    return $query->where('one_session', 1);
  }

  public function scopePassTrimester($query, $value = true)
  {
    $query->where('pass_trimester', $value);
  }

  public function getPassCategoryAttribute()
  {
    if ($this->pass_trimester == 1) {
      return 'trimester';
    } elseif ($this->number_sessions == 1) {
      return 'one_session';
    } else {
      return 'other';
    }
  }

  public function getActivitiesIdAttribute()
  {
    return Cache::remember(
      "pass_{$this->id}_activities_id",
      60 * 60 * 6,
      fn () =>
      $this->activities()->pluck('id')
    );
  }

  public function activities()
  {
    return $this->belongsToMany(Activity::class, 'pass_activity')
      ->orderBy('activities.name')
      ->withPivot('number_activity_sessions')
      ->withPivot('group_id')
      ->leftJoin('activity_groups_of_a_pass', "activity_groups_of_a_pass.id", "=", "pass_activity.group_id")
      ->select(DB::raw('activities.*, activity_groups_of_a_pass.name as group_name'));
  }

  public function activitiesNotAttached()
  {
    $query = Activity::whereNotIn('id', $this->belongsToMany(Activity::class, 'pass_activity')->pluck('id'));
    if ($this->care) {
      $query->where('care', true);
    }
    return $query;
  }

  public function establishments()
  {
    return $this->belongsToMany(Establishment::class, 'establishments_pass');
  }

  public function category()
  {
    return $this->hasOne(PassCategory::class, 'id');
  }

  public function seasons()
  {
    return $this->belongsToMany(Season::class, 'season_pass', 'pass_id');
  }

  public function price()
  {
    if (request()->season_id) {
      return $this->hasOne(Price::class, 'pass_id')->where('prices.season_id', request()->season_id);
    } else {
      return $this->hasOne(Price::class, 'pass_id')->where('establishment_id', null);
    }
  }

  public function activity_groups()
  {
    return $this->hasMany(ActivityPassGroup::class, 'pass_id')->with('activities');
  }
}
