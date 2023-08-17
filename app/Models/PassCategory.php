<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AppModel;

class PassCategory extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'date',
    'amount',
    'reference',
  ];

  public function passes()
  {
    return $this->belongsTo(Pass::class, 'pass_categories', 'pass_category_id');
  }
}
