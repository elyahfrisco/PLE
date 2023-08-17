<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ActivityApiController extends Controller
{
  public function index()
  {
    $activities = Cache::remember('activities_list', 60 * 60 * 12 /* hours */, function () {
      return Activity::orderBy('name')->search()->filter()->get();
    });

    return compact('activities');
  }
}
