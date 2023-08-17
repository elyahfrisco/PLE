<?php

namespace App\Http\Controllers\Customer;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Establishment;
use App\Models\Planning;
use App\Http\Controllers\Controller;

class PlanningController extends Controller
{
  public function index($establishment)
  {
    $data = [
      "establishment" => Establishment::findOrFail($establishment),
    ];
    if (request()->all()) {
      $data = array_merge($data, request()->all());
    }

    return Inertia::render('Customer/Planning/index', $data);
  }

  public function myPlaning()
  {
    $plannings = [];
    // $plannings = Planning::all();
    // dd($plannings);
    return Inertia::render('Customer/MyPlanning/index',  compact('plannings'));
  }
}
