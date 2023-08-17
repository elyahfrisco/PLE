<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'price',
    'reduced_price',
    'reduced_price2',
    'trimester_id',
    'activity_id',
    'pass_id',
    'season_id',
    'establishment_id',
    'type',
    'category',
  ];

  public function scopeRelatedTables($q, $activity_id = null)
  {
    if (isset(request()->getPrices['activity_id']) || !is_null($activity_id)) {
      return $this
        ->leftJoin('activities', 'activities.id', '=', 'prices.activity_id')
        ->leftJoin('passes', 'passes.id', '=', 'prices.pass_id')
        ->leftJoin('trimesters', 'trimesters.id', '=', 'prices.trimester_id')
        ->select(DB::raw('prices.*, activities.name activity, passes.name pass, trimesters.num_trimester'));
    }
    return $this
      ->leftJoin('passes', 'passes.id', '=', 'prices.pass_id')
      ->leftJoin('trimesters', 'trimesters.id', '=', 'prices.trimester_id')
      ->select(DB::raw('prices.*, passes.name pass, trimesters.num_trimester'));
  }
}
