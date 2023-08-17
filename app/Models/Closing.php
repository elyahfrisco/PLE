<?php

namespace App\Models;

use Carbon\CarbonPeriod;
use App\Models\Trimester;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Closing extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'date_start',
    'date_end',
    'reason',
    'trimester_id',
    'season_id',
    'establishment_id',
  ];

  // protected $dates = [
  //     'date_start',
  //     'date_end',
  // ];

  public function scopeFilter($query, $filter)
  {
    if (is_numeric($filter->get('establishment_id'))) {
      $query->where('establishment_id', $filter->get('establishment_id'));
    }

    if ($filter->get('minDate')) {
      $query->whereRaw("date_start >= ? OR ( date_start < ? AND date_end >= ? ) ", [$filter->get('minDate'), $filter->get('minDate'), $filter->get('minDate')]);
      // $query->where(function ($query) use ($filter) {
      //     $query->where('date_start', '>=', $filter->get('minDate'));
      //     $query->Where('date_end', '<=', $filter->get('minDate'));
      // });
    }

    if ($filter->get('maxDate')) {
      $query->whereRaw("date_end <= ? OR ( date_start <= ? AND date_end > ? ) ", [$filter->get('minDate'), $filter->get('minDate'), $filter->get('minDate')]);
      // $query->where(function ($query) use ($filter) {
      //     $query->where('date_start', '>=', $filter->get('maxDate'));
      //     $query->Where('date_end', '<=', $filter->get('maxDate'));
      // });
    }

    if (is_numeric($filter->get('season_id'))) {
      $season = Season::find($filter->get('season_id'));
      $query
        ->where('establishment_id', $season->establishment_id)
        ->where('date_start', '>=', $season->date_start)
        ->where('date_end', '<=', $season->date_end);
    }

    return $query;
  }

  public function scopeSelectDaysClosingCount($query)
  {
    return $query->selectRaw('*, (DATEDIFF(date_end, date_start) + 1) AS days_closing');
  }

  public function trimester()
  {
    return $this->belongsTo(Trimester::class);
  }

  public function getPeriodeDatesAttribute()
  {
    return CarbonPeriod::create($this->date_start, $this->date_end)->toArray();
  }
}
