<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AppModel;

class SubscriptionComment extends AppModel
{
  use HasFactory;
  use GetterTrait;

  protected $fillable = ['content', 'user_subscription_id', 'user_id'];
  protected $table = 'subscription_comments';
  protected $appends = ['created_at_fr'];

  public function author()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($subscription_comment) {
      $subscription_comment->user_id = auth()->user()->id;
    });
  }
}
