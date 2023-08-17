<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AppModel;

class FollowedUser extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'accepted',
    /** change to confirmation date */
    'acceptation_date',
    'user_follower_id',
    'user_following_id',
  ];
  protected $table = 'followed_user';


  public function following()
  {
    return $this
      ->hasOne(User::class, 'id', 'user_following_id');
  }

  public function follower()
  {
    return $this
      ->hasOne(User::class, 'id', 'user_follower_id');
  }
}
