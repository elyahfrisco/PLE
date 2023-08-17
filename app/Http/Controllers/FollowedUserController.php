<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\FollowedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class FollowedUserController extends Controller
{
  public function __construct(Request $request)
  {
    // if ($request->cookie('guest_mode') && request()->route()->getName() != 'following.cookie.clear') {
    //     abort(403, 'Accès non autorisé');
    // }
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return Inertia::render('Account/FolowedUser/index.vue');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function admin_index()
  {
    return Inertia::render('Account/FolowedUser/Admin/index.vue');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function sendFollowRequest()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    FollowedUser::create($request->all());
    return back();
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  /** switch to user account */
  public function show($id)
  {
    $following = FollowedUser::findOrFail($id);

    if ($id != auth()->user()->id) {
      $follower = auth()->user();

      abort_if((($following && ((auth()->user()->id != $following->user_follower_id && !auth()->user()->isAdmin())))), 403, 'accès refusé');

      $user = User::find($following->user_following_id);

      /** Changer le compte connecté */
      // Auth::guard('web')->loginUsingId($user->id);
      Auth::loginUsingId($user->id);

      request()->session()->regenerate();

      /** delay utilisation compte du client suivi */
      $minutes = 60;

      return redirect()->intended('dashboard')->withCookie(cookie('guest_mode', $follower, $minutes));
    }
    return response('ok');
  }

  public function cookie_clear()
  {
    $cookie = Cookie::forget('guest_mode');
    return response('ok')->cookie($cookie);
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
    FollowedUser::find($id)->update([
      'accepted' => $request->accepte,
      'acceptation_date' => now(),
    ]);

    if ($request->accepte) {
      session()->flash('success', "Demande de suivi confirmé");
    } else {
      session()->flash('info', "Demande de suivi refusé");
    }
    return back();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    FollowedUser::find($id)->delete();
    return back()->with('info', 'Demande de suivi annulée');
  }
}
