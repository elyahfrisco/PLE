<?php

namespace App\Models;

use App\Models\Establishment;
use Illuminate\Support\Facades\DB;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeriodeSubscription extends AppModel
{
  use HasFactory;
  protected $fillable = [
    'start_at',
    'end_at',
    'description',
    'establishment_id',
    'season_id',
    'pass_id',
  ];

  public function establishment()
  {
    return $this->belongsTo(Establishment::class);
  }

  public function season()
  {
    return $this->belongsTo(Season::class);
  }

  public function pass()
  {
    return $this->belongsTo(Pass::class);
  }

  public function scopeStatut()
  {
    return $this->select(DB::raw("periode_subscriptions.*, IF(end_at < NOW(),'termined', IF(start_at > NOW(), 'futur', 'in_progress')) as status"));
  }
}
