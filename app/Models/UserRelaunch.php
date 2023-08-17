<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRelaunch extends AppModel
{
  use HasFactory;
  protected $table = 'user_relaunchs';

  protected $fillable = [
    'subject',
    'content',
    'relaunch_type',
    'date_relaunch',
    'executed',
    'user_id',
    'id_group',
    'season_id',
    'pass_id',
    'num_trimester',
  ];

  protected $appends = ['elapseTime', 'timeSpent'];

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function getElapseTimeAttribute()
  {
    return Carbon::parse($this->date_relaunch)->diffForHumans();
  }

  public function getTimeSpentAttribute()
  {
    return Carbon::parse($this->date_relaunch) < now();
  }

  public function scopeFilter($query)
  {
    if (is_numeric(request()->user_id)) {
      $query->where('user_id', request()->user_id);
    }

    if (request()->type === 'prospect') {
      $query->whereHas('user', function ($q) {
        $q->ProspectRole();
      });
    }

    if (request()->not_executed) {
      $query->whereExecuted(false);
    }

    if (request()->date) {
      $query->whereDate('date_relaunch', request()->date);
    }
  }
}
