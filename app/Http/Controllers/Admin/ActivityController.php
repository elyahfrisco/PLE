<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Activity;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Establishment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\ActivityRequest;

class ActivityController extends Controller
{

  public function __construct()
  {
    $this->middleware(['auth:sanctum', 'verified']);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $activities = Cache::remember('activities_list', 60 * 60 * 12 /* hours */, function () {
      return Activity::orderBy('name')
        ->search()
        ->filter()
        ->with([
          'activities_for_recuperation' => function ($query) {
            $query->orderBy('name')
              ->select('id', 'name');
          },
        ])
        ->get();
    });

    return renderJsonOrInertia('Admin/Activity/index', compact('activities'));
  }

  public function _style_css()
  {
    $style = Cache::rememberForever('activities_styles', function () {
      $style = '';
      $d = [];
      foreach (Activity::all() as $key => $activity) {
        $class_ = clear_accent($activity->name);

        $style_ = "
            .session-activity-$class_{
            background-color: $activity->background_color !important;
            color: $activity->font_color !important;
            }
            ";

        $style_ .= "
            .session-activity-$activity->id{
            background-color: $activity->background_color !important;
            color: $activity->font_color !important;
            }
            ";

        $style .= $style_;

        $d[] = $style_;
      }
      return $style;
    });

    die($style);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $establishments = Establishment::get();
    return Inertia::render('Admin/Activity/create', compact('establishments'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(ActivityRequest $request)
  {
    Activity::create($request->all());
    forget_cache('activities_list');
    return back()->with('success', 'Activité ajouté');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    return Activity::find($id);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $activity = Activity::findOrFail($id);

    $activity->load('activities_for_recuperation');
    return Inertia::render('Admin/Activity/edit', compact('activity'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(ActivityRequest $request, $id)
  {
    $activity = Activity::find($id);
    $activity->update($request->except('activities_for_recuperation_id'));
    if ($request->file('photo')) {
      $image_path = $request->file('photo')->storeAs('images/activites', $request->file('photo')->getClientOriginalName(), 'public_path');
      $activity->update(['image' => $image_path]);
    }
    $activity->activities_for_recuperation()->sync($request->activities_for_recuperation_id);
    forget_cache('activities_list');
    return back()->with('success', 'Activité modifié');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    Activity::destroy($id);
    forget_cache('activities_list');
    return back()->with('success', 'Activité supprimé');
  }
}
