<?php

namespace App\Models;

use App\Models\Post;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostCategory extends AppModel
{
  use HasFactory;
  protected $fillable = [
    'name',
    'color',
  ];

  public function posts()
  {
    return $this->hasMany(Post::class, 'post_category_id');
  }
}
