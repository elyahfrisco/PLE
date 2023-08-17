<?php

namespace App\Models;

trait GetterTrait
{
  public function getCreatedAtFrAttribute()
  {
    return $this->created_at->format('d/m/Y H:i');
  }

  public function getContentNoTagAttribute()
  {
    return str_replace('&nbsp;', ' ', strip_tags($this->content));
  }
}
