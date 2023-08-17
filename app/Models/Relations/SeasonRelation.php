<?php

namespace App\Models\Relations;

use App\Models\Pass;
use App\Models\Price;
use App\Models\Planning;
use App\Models\Trimester;
use App\Models\Establishment;
use Illuminate\Support\Facades\DB;

trait SeasonRelation
{
  public function establishment()
  {
    return $this->belongsTo(Establishment::class);
  }

  public function trimesters()
  {
    if (request()->getPrices) {
      return $this->hasMany(Trimester::class)
        ->leftJoin('prices', function ($price) {
          $price->on('prices.trimester_id', '=', 'trimesters.id');
          $price->on('prices.activity_id', '=', DB::raw(request()->getPrices['activity_id']));
        })
        ->orderBy('num_trimester')
        ->groupBy('trimesters.num_trimester')
        ->select(DB::raw('trimesters.*, prices.price, prices.reduced_price, prices.reduced_price2'));
    }
    return $this->hasMany(Trimester::class)->orderBy('num_trimester');
  }

  public function trimesters_prices()
  {
    return $this->hasMany(Trimester::class)->orderBy('num_trimester');
  }

  public function passes()
  {
    return $this->belongsToMany(Pass::class, 'season_pass', 'season_id')->orderBy('name');
  }

  public function passesOneSession()
  {
    if (request()->getPrices) {
      return $this->belongsToMany(Pass::class, 'season_pass', 'season_id')
        ->leftJoin('prices', function ($price) {
          $price->on('prices.pass_id', '=', 'passes.id');
          $price->on('prices.activity_id', '=', DB::raw(request()->getPrices['activity_id']));
          $price->on('prices.season_id', '=', DB::raw(request()->getPrices['season_id']));
        })
        ->orderBy('name')
        ->groupBy('passes.id')
        ->where('one_session', 1)
        ->select(DB::raw('passes.*, prices.price, prices.reduced_price, prices.reduced_price2'));
    }
    return $this->belongsToMany(Pass::class, 'season_pass', 'season_id')->where('one_session', 1)->orderBy('name');
  }

  public function plannings()
  {
    return $this->hasMany(Planning::class);
  }

  public function registration_price()
  {
    return $this->hasMany(Price::class, 'season_id')->where('prices.type', 'registration');
  }

  public function management_price()
  {
    return $this->hasMany(Price::class, 'season_id')->where('prices.type', 'management');
  }
}
