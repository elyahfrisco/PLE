<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AppModel;

class PassActivity extends AppModel
{
  use HasFactory;
  protected $fillable = [
    'number_activity_sessions',
    'price_per_person',
    'pass_id',
    'activity_id',
    'group_id',
  ];
  protected $table = 'pass_activity';
}
