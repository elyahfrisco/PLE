<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Season;
use App\Models\Establishment;
use Illuminate\Support\Facades\DB;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Planning extends AppModel
{
  use HasFactory;
  use SoftDeletes;

  protected $fillable = [
    'day',
    'time_start',
    'time_end',
    'start_at',
    'end_at',
    'max_effective',
    'super_pass',
    'finished_at',
    'number_activity_sessions',
    'organized',
    'establishment_id',
    'season_id',
    'activity_id',
    'hide_to_customer',
  ];

  protected $casts = [
    'time_start' => 'datetime:H:i',
    'time_end' => 'datetime:H:i',
    'start_at' => 'datetime:Y-m-d',
    'end_at' => 'datetime:Y-m-d',
  ];

  protected $date = [
    'finished_at',
  ];

  protected $appends = ['group_name'];

  public function establishment()
  {
    return $this->belongsTo(Establishment::class);
  }

  public function season()
  {
    return $this->belongsTo(Season::class);
  }

  public function activity()
  {
    return $this->belongsTo(Activity::class);
  }

  public function scopeOrderAsc($query)
  {
    return $query->orderBy('time_start');
  }

  public function activitySessions()
  {
    return $this->hasMany(ActivitySessions::class);
  }

  public function getViewClassAttribute()
  {
    $time_start = $this->time_start->diff($this->time_end);
    return "$time_start->h-$time_start->i";
  }


  public function getDayNameAttribute()
  {
    return substr(dToFr(Carbon::parse($this->start_at)->format('l')), 0, 2);
  }

  public function SigleEstablishmentName()
  {
    if ($this->relationLoaded('establishment')) {
      return strtolower($this->establishment->name) == 'bordeaux' ? 'C' : $this->establishment->name[0];
    } else if ($this->establishment_name) {
      return strtolower($this->establishment_name) == 'bordeaux' ? 'C' : $this->establishment_name[0];
    } else {
      return "";
    }
  }

  public function getGroupNameAttribute()
  {
    return strtoupper($this->day_name) . Carbon::parse($this->time_start)->format('H\Hi') . $this->SigleEstablishmentName();
  }

  public function scopeWTrimester($query, $season_id, $num_trimester)
  {
    $trimesterLimit = Trimester::where('season_id', $season_id)
      ->where('num_trimester', $num_trimester)
      ->select(DB::raw('MIN(date_start) date_start, MAX(date_end) date_end'))
      ->first();

    dmq($trimesterLimit);

    $query->where(function ($q) use ($trimesterLimit) {
      $q->whereRaw(" (start_at >= '$trimesterLimit->date_start' AND start_at <= '$trimesterLimit->date_end') ")
        ->orWhereRaw(" (end_at >= '$trimesterLimit->date_start' AND end_at <= '$trimesterLimit->date_end') ")
        ->orWhereRaw(" (start_at >= '$trimesterLimit->date_start' AND end_at >= '$trimesterLimit->date_end') ");
    })
      ->orderBy('time_start');

    return $query;
  }

  public function scopeIsActivityForPass($query, $pass_id)
  {
    if ($pass_id)
      $query->whereIn('activity_id', Pass::findInCache($pass_id)->activities()->select('activities.id'));
  }

  public function scopeSearch($query)
  {
    if (request()->filled('q')) {
      $query->whereRaw("
                (
                    search_key LIKE '%" . trim(request()->q) . "%'
                )
                ")->limit(20);
    }

    if (request()->filled('planning_id')) {
      $query->whereId(request()->planning_id);
    }
  }

  public function scopeGroupByDayIf($query, $condition)
  {
    if ($condition)
      $query->groupBy('day')->select(DB::raw('*, count(*) as total'));
  }

  public function scopeWhereStartAfterNow($query, $condition)
  {
    if ($condition) {
      $query->where(function ($where_query) {
        $start_date = now();
        $where_query->where('start_at', '>=', $start_date)
          ->orWhere('end_at', '>=', $start_date)
          ->orderBy('time_start');
      });
    }
  }

  public function scopeWhereDateStartAt($query, $date_start)
  {
    if ($date_start)
      $query->where(function ($where_query) use ($date_start) {
        $where_query->where('start_at', '>=', $date_start)
          ->orWhere('end_at', '>=', $date_start);
      });
  }

  public function updateSearchKey()
  {
    $this->load('establishment', 'activity');
    $search_key = " $this->group_name " . dToFr(Carbon::parse($this->start_at)->format('l'));

    if ($search_key != $this->search_key) {
      $this->search_key = $search_key;
      $this->save();
      Log::info("planning $this->id : " . $this->search_key);
    }
  }

  protected static function boot()
  {
    parent::boot();

    static::created(function ($planning) {
      $planning->updateSearchKey();
    });
    static::updated(function ($planning) {
      $planning->updateSearchKey();
    });
  }
}
