<?php

namespace App\Models;

use App\Models\User;
use App\Models\Permission;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends AppModel
{
  use HasFactory;

  protected $fillable = ['name', 'deletable'];

  public function users()
  {
    return $this->belongsToMany(User::class, 'role_user');
  }

  public function permissions()
  {
    return $this->belongsToMany(Permission::class, 'permission_role');
  }
}
