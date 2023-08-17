<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\UserRelaunch;
use Illuminate\Http\Request;
use App\Jobs\SendRelaunchMailJob;
use App\Models\mailTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;

class UserRelaunchController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $relaunchs = $this->_list(request());
    return Inertia::render('Relaunch/index', compact('relaunchs'));
  }

  public function _list(Request $request)
  {
    $relaunchsQuery = UserRelaunch::with('user')->filter();
    $relaunchsQuery->orderByRaw('executed ASC, TIMEDIFF(date_relaunch, NOW()), date_relaunch ASC');
    return $relaunchsQuery->paginate(page_limit());
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return Inertia::render('Relaunch/create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    if ($request->save_like_a_template_only) {
      mailTemplate::create([
        'title' => $request->subject,
        'content' => $request->content,
      ]);
      return redirect()->route('relaunchs.create')->with('success', 'Modèle enregistré');
    }

    $request->validate([
      'content' => 'required',
    ]);

    $data = $request->except('time_relaunch');

    if ($request->sendDirectly) {
      $data['date_relaunch'] = now()->format('Y-m-d H:i');
    } else {
      $data['date_relaunch'] = $request->date_relaunch . " " . $request->time_relaunch;
    }

    $data['id_group'] = time();

    /** Enregistrer en tant que modèle */
    if ($request->save_like_a_template === true) {
      mailTemplate::create([
        'title' => $request->subject,
        'content' => $request->content,
      ]);
    }

    foreach ($data['users_id'] as $key => $user_id) {
      $data_ = $data;
      $data_['user_id'] = $user_id;
      $relaunch = UserRelaunch::create($data_);
    }

    return redirect()->route('relaunchs.create')->with('success', 'Relance programmée');
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
  public function update(Request $request, $id)
  {
    //
  }

  public function _send(Request $request)
  {
    UserRelaunch::whereId($request->relaunch_id)->update(['date_relaunch' => now()]);
    return back()->with('success', "la relance sera envoyé dans 1 minute");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $user_relaunch = UserRelaunch::find($id);
    $user_relaunch->delete();
    return back()->with('success', 'Relance supprimée');
  }
}
