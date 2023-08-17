<?php

namespace App\Models;

use App\Models\ActivitySessions;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecuperationRequest extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'content',
    'presence_confirmed_at',
    'generate_automatically',
    'activity_session_id_to_catch_up',
    'activity_session_id_for_catch_up',
    'queue_id',
    'user_id',
  ];

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function session_to_catch_up()
  {
    return $this->hasOne(ActivitySessions::class, 'id', 'activity_session_id_to_catch_up')->withCount('participants');
  }

  public function session_for_catch_up()
  {
    return $this->hasOne(ActivitySessions::class, 'id', 'activity_session_id_for_catch_up')->withCount('participants');
  }
}
