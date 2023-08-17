<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pass;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityPassGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PassActivity;

class ActivityPassGroupController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, $pass_id)
  {
    $pass = Pass::find($pass_id);
    // $request->request->set('name', 'G-' . Str::camel(ucfirst(strtolower($pass->name . ' ' .  $request->select_mode)) . date('s')));
    ActivityPassGroup::create($request->all());
    session()->flash('success', "Groupe d'activité créé");
    return back();
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $pass_id, ActivityPassGroup $group)
  {
    $group->update($request->all());
    session()->flash('success', "Groupe d'activité modifié");
    return back();
  }

  public function move_activity($pass_id, $activity_id, $group_id)
  {
    PassActivity::where('pass_id', $pass_id)
      ->where('activity_id', $activity_id)
      ->update(['group_id' => $group_id]);

    session()->flash('success', 'Activité deplacé vers le groupe');
    return back();
  }

  public function detach_activity($pass_id, $activity_id, $group_id)
  {
    PassActivity::where('pass_id', $pass_id)
      ->where('activity_id', $activity_id)
      ->update(['group_id' => null]);
    session()->flash('success', 'Activité retiré du groupe');
    return back();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(ActivityPassGroup $group)
  {
    abort_if(!$group, 404);

    $group_id = $group->id;
    $group->delete();

    PassActivity::where('group_id', $group_id)->update(['group_id' => null]);

    session()->flash('success', "Groupe d'activité supprimé");

    return back();
  }
}
