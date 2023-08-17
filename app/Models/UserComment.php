<?php

namespace App\Models;

use App\Models\User;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserComment extends AppModel
{
  use HasFactory;
  use SoftDeletes;

  protected $table = 'user_comments';
  protected $fillable = ['content', 'user_id', 'commentator_id'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function commentator()
  {
    return $this->belongsTo(User::class, 'commentator_id');
  }
}
