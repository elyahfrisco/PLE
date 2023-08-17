<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AppModel;

class ContactOrigin extends AppModel
{
  use HasFactory;

  protected $fillable = ['designation'];
}
