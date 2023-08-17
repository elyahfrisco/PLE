<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AppModel;
use Carbon\Carbon;

class Bug extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'title',
    'content',
    'page',
    'user_id',
    'resolved',
  ];

  public function getCreatedAtAttribute($value)
  {
    return Carbon::parse($value)->format('d/m/Y H:i');
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
