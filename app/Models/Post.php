<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends AppModel
{
  use HasFactory;
  protected $fillable = [
    'title',
    'slug',
    'content',
    'cover_photo_path',
    'user_id',
    'post_category_id',
  ];

  protected $appends = ['elapseTime', 'created_at_fr', 'content_min', 'content_no_tag'];
  protected $with = ['author'];

  public function author()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function category()
  {
    return $this->hasOne(PostCategory::class, 'id', 'post_category_id');
  }

  public function getContentNoTagAttribute()
  {
    return Str::limit(strip_tags($this->content), 300);
  }

  public function getContentMinAttribute()
  {
    return Str::limit(strip_tags($this->content), 20);
  }

  public function getElapseTimeAttribute()
  {
    return $this->created_at->diffForHumans();
  }

  public function getCreatedAtFrAttribute()
  {
    return $this->created_at->format("d/m/Y");
  }

  public function scopeOrder($query)
  {
    return $query->when(request()->has('sortBy') && in_array(request()->sortBy, array_merge($this->fillable, ['created_at', 'id'])), function ($query) {
      if (request()->get('sortBy') == 'category_name') {
        $query->leftJoin('post_categories AS p_c', 'p_c.id', '=', 'posts.post_category_id')
          ->orderBy('p_c.name', request()->sortDirection);
      } else if (request()->get('sortBy') == 'author_name') {
        $query->leftJoin('users AS u_', 'u_.id', '=', 'posts.user_id')
          ->orderBy('u_.first_name', request()->sortDirection);
      } else {
        $query->orderBy(
          request()->sortBy,
          ((in_array(strtolower(request()->sortDirection), ['asc', 'desc'])) ? request()->sortDirection : 'asc')
        );
      }
      $query->select('posts.*');
    })->when(!request()->has('sortBy'), function ($query) {
      $query->orderByRaw('title ASC');
    });
  }

  public function scopeFilter($query)
  {
    if (is_numeric(request()->filterBy['post_category_id'] ?? false)) {
      $query->where('post_category_id', request()->filterBy['post_category_id']);
    }
  }

  public function scopeSearch($query)
  {
    return $query->when(request()->q, function ($query, $q) {
      $query->whereRaw("(
                content LIKE '%$q%'
                )");
      $query->orWhereRaw("(
                title LIKE '%$q%'
                )");
    });
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($post) {
      if (!$post->slug)
        $post->slug = Str::slug($post->title);
    });

    static::updating(function ($post) {
      $post->slug = Str::slug($post->title);
    });
  }
}
