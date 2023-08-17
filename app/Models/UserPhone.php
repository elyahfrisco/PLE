<?php

namespace App\Models;

use App\Models\User;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class UserPhone extends AppModel
{
  use HasFactory;
  protected $table = 'user_phones';

  protected $fillable = [
    'phone',
    'owner',
    'type',
    'user_id',
  ];

  public function user()
  {
    $this->belongsTo(User::class);
  }

  public function updateSearchKey()
  {
    $search_key = str_replace([' ', '.', '-'], ['', '', ''], $this->phone);
    if ($search_key != $this->search_key) {
      $this->search_key = $search_key;
      $this->save();
      Log::info("phone $this->phone : " . $this->search_key);
    }
  }

  protected static function boot()
  {
    parent::boot();

    static::created(function ($phone) {
      $phone->updateSearchKey();
    });
    static::updated(function ($phone) {
      $phone->updateSearchKey();
    });
  }
}
