<?php

namespace App\Models;

use App\Models\AppModel;
use App\Models\Relations\SeasonRelation;
use App\Models\Scopes\SeasonScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Season extends AppModel
{
  use HasFactory;
  use SeasonScope;
  use SeasonRelation;

  protected $fillable = [
    'date_start',
    'date_end',
    'year_start',
    'year_end',
  ];

  protected $dates = [
    'date_start',
    'date_end',
  ];

  protected $appends = [
    'season_txt',
    'status',
  ];

  public function getSeasonTxtAttribute()
  {
    return $this->year_start . ' - ' . $this->year_end;
  }

  public function getStatusAttribute()
  {
    if ($this->date_end >= now() &&  $this->date_start  <= now())
      return 'in_progress';
    elseif ($this->date_end <= now())
      return 'finished';

    return 'upcoming';
  }

  protected static function boot()
  {
    parent::boot();

    static::created(function ($season) {
      forget_cache("establishment_{$season->establishment_id}_seasons");
    });
    static::updated(function ($season) {
      forget_cache("establishment_{$season->establishment_id}_seasons");
    });
    static::deleted(function ($season) {
      forget_cache("establishment_{$season->establishment_id}_seasons");
    });
  }
}
