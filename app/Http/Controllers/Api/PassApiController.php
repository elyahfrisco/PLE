<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pass;
use App\Models\Planning;
use Illuminate\Http\Request;

class PassApiController extends Controller
{
    public function activities(Request $request, Pass $pass)
    {
        $activities  = $pass->activities();

        if ($request->season_id) {
            $activities_id = Planning::where('organized', true)->where('season_id', $request->season_id)->groupBy('activity_id');
            if ($request->day) {
                $activities_id->where('day', $request->day);
                $activities->whereIn('activities.id', $activities_id->pluck('activity_id'));
            } else {
                $activities->whereIn('activities.id', $activities_id->pluck('activity_id'));
            }
        }

        return $activities->get();
    }
}
