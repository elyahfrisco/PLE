<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AppModel;

class mailTemplate extends AppModel
{
  use HasFactory;
  protected $fillable = [
    'title',
    'content',
  ];

  protected $table = 'mail_templates';
}
