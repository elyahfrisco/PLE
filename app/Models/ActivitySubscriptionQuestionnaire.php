<?php

namespace App\Models;

use App\Models\AppModel;
use App\Models\ActivitySubscriptionQuestionAnswer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivitySubscriptionQuestionnaire extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'type',
    'content',
    'other_response',
    'activity_id',
    'other_response_placeholder',
  ];


  public function answers()
  {
    return $this->hasMany(ActivitySubscriptionQuestionAnswer::class);
  }
}
