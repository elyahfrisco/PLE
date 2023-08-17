<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StatisticApiController extends Controller
{
  public function prospectList()
  {
    return User::prospectRole()->limit(15)->orderBy('created_at', 'DESC')->get();
  }
}
