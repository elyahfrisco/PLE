<?php

namespace App\Models;

use App\Models\User;
use App\Models\Establishment;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'object',
    'content',
    'user_id',
    'establishment_id',
  ];

  public function establishment()
  {
    return $this->belongsTo(Establishment::class);
  }
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function scopeSearch($query)
  {
    return $query->when(request()->q, function ($query, $q) {

      $query->whereRaw("(
                object LIKE '%$q%'
                OR content LIKE '%$q%'
                )");

      $query->OrWhereHas('user', function ($query) use ($q) {
        $query->whereRaw("(
                    name LIKE '%$q%'
                    OR first_name LIKE '%$q%'
                    OR email LIKE '%$q%'
                    OR (LOWER(CONCAT(first_name, ' ', name)) LIKE LOWER('%$q%'))
                    OR (LOWER(CONCAT(name, ' ', first_name)) LIKE LOWER('%$q%'))
                        )");
      });
      $query->OrWhereHas('establishment', function ($query) use ($q) {
        $query->whereRaw("(
                    name LIKE '%$q%'
                        )");
      });
    });
  }

  public function scopeOrder($query)
  {
    return $query->when(request()->has('sortBy') && in_array(request()->sortBy, array_merge($this->fillable, ['created_at', 'first_name', 'establishment_name'])), function ($query) {
      $direction = ((in_array(strtolower(request()->sortDirection), ['asc', 'desc'])) ? request()->sortDirection : 'asc');
      if (request()->get('sortBy') == 'first_name') {
        $query->leftJoin('users AS u_', 'u_.id', '=', 'contacts.user_id')
          ->orderBy('u_.first_name', $direction);
      } elseif (request()->get('sortBy') == 'establishment_name') {
        $query->leftJoin('establishmentes AS e_', 'e_.id', '=', 'contacts.establishment_id')
          ->orderBy('e_.name', $direction);
      } else {
        $query->orderBy(
          request()->sortBy,
          $direction
        );
      }
    })->when(!request()->has('sortBy'), function ($query) {
      $query->orderByRaw('contacts.created_at DESC');
    });
  }
}
