<?php

namespace App\Http\Controllers\Intervenant;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Establishment;
use App\Http\Controllers\Controller;

class PlanningController extends Controller
{
  public function index(Establishment $establishment)
  {
    $data = [
      "establishment" => Establishment::findOrFail($establishment),
    ];
    if (request()->all()) {
      $data = array_merge($data, request()->all());
    }
    return Inertia::render('Intervenant/Planning/index', $data);
  }
}
