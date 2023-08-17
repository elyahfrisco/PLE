<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Notifications\Dev\NewBugNotification;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class BugController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    /** Page Liste bug */
    $bugs = Bug::latest()->with(['user'])->paginate();
    return Inertia::render('Bug/index', compact('bugs'));
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    /** Page formulaire bug */
    return Inertia::render('Bug/create');
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $bug = Bug::create($request->all());

    // $notif = Notification::route('mail', 'dev06@kawa-group.fr')
    //   ->notify(new NewBugNotification($bug));

    return redirect($bug->page)->with('success', 'Bug signalé');
  }

  public function update(Request $request, $id)
  {
    $bug = Bug::find($id)->update($request->all());
    return redirect()->route('bugs.index')->with('success', 'Satutus changé');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $bug = Bug::find($id);
    // return Inertia::render('Bug/show', compact('bug'));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $bug = Bug::find($id);
    $bug->delete();
    return redirect()->route('bugs.index')->with('success', 'Message supprimé');
  }
}
