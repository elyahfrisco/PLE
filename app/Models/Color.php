<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AppModel;

class Color extends AppModel
{
  use HasFactory;

  protected $fillable = [
    'background',
    'font',
  ];
}
