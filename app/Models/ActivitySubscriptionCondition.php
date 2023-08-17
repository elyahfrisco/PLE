<?php

namespace App\Models;

use App\Models\Activity;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivitySubscriptionCondition extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'content',
    'activity_id',
  ];

  public function activity()
  {
    return $this->belongsTo(Activity::class);
  }
}
