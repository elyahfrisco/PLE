<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends AppModel
{
  use HasFactory;

  protected $fillable = [
    "amount",
    "user_id",
    "predictable_payment_date",
    "payment_method",
    "season_id",
    "establishment_id",
    "is_imported",
  ];

  protected $appends = ['elapseTime'];

  public function subscriptions()
  {
    return $this->hasMany(Subscription::class);
  }

  public function fees()
  {
    return $this->hasMany(UserFee::class);
  }

  public function is_paid()
  {
    return $this->payment !== null ? true : false;
  }

  public function payment()
  {
    return $this->hasOne(Payment::class, 'bill_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function season()
  {
    return $this->belongsTo(Season::class);
  }

  public function getElapseTimeAttribute()
  {
    return $this->predictable_payment_date ? Carbon::parse($this->predictable_payment_date)->diffForHumans() : 'no défini';
  }

  public function scopeSearch($query, $fields = null)
  {
    if (preg_match("/client_id/i", request()->q)) {
      $q_array = explode(':', request()->q);
      $key = $q_array[0];
      $q = $q_array[1] ?? '';

      switch ($key) {
        case "client_id":
          $query->where('bills.user_id', $q);
          break;
      }
    } else {
      $fields = $fields ? $fields : ['amount', 'payment_method'];
      return $query->when(request()->q, function ($query_w1, $q) use ($fields) {
        $query_w1->where(function ($query_w2) use ($q, $fields) {
          foreach ($fields as $key => $field) {
            $query_w2->orWhereRaw("LOWER($field) LIKE LOWER('%$q%')");
          }

          $query_w2->OrWhereHas(
            'user',
            function ($query) use ($q) {
              $query->whereRaw("(
                            name LIKE '%$q%'
                            OR first_name LIKE '%$q%'
                            OR (LOWER(CONCAT(first_name, ' ', name)) LIKE LOWER('%$q%'))
                            OR (LOWER(CONCAT(name, ' ', first_name)) LIKE LOWER('%$q%'))
                            )");
            }
          );
        });
      });
    }
  }

  public function scopeFilter($query)
  {
    $query->whereHas('season');

    if (request()->min_date) {
      $query->whereDate('predictable_payment_date', '>=', Carbon::parse(request()->min_date)->format('Y-m-d'));
    }

    if (request()->max_date) {
      $query->whereDate('predictable_payment_date', '<=', Carbon::parse(request()->max_date)->format('Y-m-d'));
    }

    if (is_numeric(request()->planning_id)) {
      $query->whereHas('subscriptions', function ($sub) {
        $sub->whereHas('activities', function ($activities) {
          $activities->where('planning_id', request()->planning_id);
        });
      });
    }

    if (is_numeric(request()->pass_id)) {
      $query->whereHas('subscriptions', function ($sub) {
        $sub->where('pass_id', request()->pass_id);
      });
    }

    if (is_numeric(request()->establishment_id)) {
      $query->where('establishment_id', request()->establishment_id);
    }
  }
}
