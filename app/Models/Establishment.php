<?php

namespace App\Models;

use App\Models\Pass;
use App\Models\Activity;
use App\Models\Planning;
use Illuminate\Support\Facades\DB;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Establishment extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'name',
    'address',
    'postal_code',
    'phone',
    'email',
    'start_time',
    'end_time',
    'latitude',
    'longitude',
    'relaxation_center',
  ];

  protected $appends = ['sigle'];

  /** Attribute */
  public function getSigleAttribute()
  {
    return $this->name == 'BORDEAUX' ? 'C' : $this->name[0];
  }

  public function setRelaxationCenterAttribute($value)
  {
    if ($value === null)
      $this->attributes['relaxation_center'] = false;
  }

  public function seasons()
  {
    return $this->hasMany(Season::class);
  }

  public function SeasonDesc()
  {
    return $this->seasons()->orderByDesc('year_end');
  }

  public function lastSeasonId()
  {
    return $this->SeasonDesc()->first()->id;
  }

  public function activities()
  {
    return $this->belongsToMany(Activity::class, 'establishment_activity')->withPivot(['out_pass'])->orderBy('name');
  }

  public function activitiesNotAttached()
  {
    $query = Activity::orderBy('name');

    if ($this->relaxation_center) {
      $query->where('care', true);
    }

    $query->whereNotIn('id', $this->belongsToMany(Activity::class, 'establishment_activity')->pluck('id'));

    return $query;
  }

  public function passes()
  {
    return $this->belongsToMany(Pass::class, 'establishments_pass');
  }

  public function passesNotAttached()
  {
    $query = Pass::whereNotIn('id', $this->belongsToMany(Pass::class, 'establishments_pass')->pluck('id'))->orderBy('name');

    if ($this->relaxation_center) {
      $query->where('care', true);
    } else {
      $query->where('care', false);
    }
    return $query;
  }

  public function plannings()
  {
    return $this->hasMany(Planning::class);
  }

  public function planningsLastSeason()
  {
    return $this->plannings()->whereNull('finished_at')->whereSeason_id($this->seasons()->orderByDesc('year_end')->first()->id ?? null);
  }

  public function planningsFiltered($filters)
  {
    $plannings = $this->plannings()->whereNull('finished_at');

    if (is_numeric($filters->season_id)) {
      $plannings->whereSeason_id($filters->season_id);
    } else {
      $plannings->whereSeason_id($this->seasons()->orderByDesc('year_end')->first()->id);
    }

    if (is_numeric($filters->num_trimester)) {
      $trimester = Trimester::select(DB::raw('*, MIN(date_start) date_start, MIN(date_end) date_end'))
        ->where('num_trimester', $filters->num_trimester)
        ->where('season_id', $filters->season_id)->first();

      $plannings->where(function ($query) use ($trimester) {

        $query->whereRaw(" (start_at >= '$trimester->date_start' AND start_at <= '$trimester->date_end') ")
          ->orWhereRaw(" (end_at >= '$trimester->date_start' AND end_at <= '$trimester->date_end') ")
          ->orWhereRaw(" (start_at >= '$trimester->date_start' AND end_at >= '$trimester->date_end') ");

        // $query->where(function ($query) use ($trimester) {
        //     $query->where('start_at', '>=', $trimester->date_start)
        //         ->Where('start_at', '<=', $trimester->date_end);
        // })
        //     ->orwhere(function ($query) use ($trimester) {
        //         $query->where('end_at', '>=', $trimester->date_start)
        //             ->Where('end_at', '<=', $trimester->date_end);
        //     });
      });
    }

    if (is_numeric($filters->activity_id)) {
      $plannings->whereActivity_id($filters->activity_id);
    }

    if ($filters->date_min) {
      $plannings->where('start_at', '>=', $filters->date_min);
    }
    if ($filters->date_max) {
      $plannings->where('start_at', '<=', $filters->date_max);
    }

    return $plannings;
  }
}
