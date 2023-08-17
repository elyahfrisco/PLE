<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AppModel;

class ActivitySubscriptionQuestionAnswer extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'content',
    'activity_subscription_questionnaire_id',
  ];
}
