<?php

namespace App\Models;

use App\Models\Subscription;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends AppModel
{
  use HasFactory;
  protected $fillable = [
    'date',
    'amount',
    'reference',
    'bill_id',
    'user_id',
    'admin_id',
    'payment_method',
    'description',
  ];

  public function subscriptions()
  {
    return $this->hasMany(Subscription::class, 'bill_id', 'bill_id');
  }
}
