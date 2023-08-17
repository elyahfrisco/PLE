<?php

namespace App\Models;

use App\Models\Activity;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityPassGroup extends AppModel
{
  use HasFactory;
  protected $fillable = [
    'name',
    'select_mode',
    'pass_id',
    'number_session',
  ];

  protected $table = 'activity_groups_of_a_pass';
  public function activities()
  {
    return $this->belongsToMany(Activity::class, 'pass_activity', 'group_id', 'activity_id');
  }
}
