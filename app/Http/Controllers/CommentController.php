<?php

namespace App\Http\Controllers;

use App\Models\UserComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($customer_id)
  {
    return response()->json(UserComment::where('user_id', $customer_id)->get());
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    UserComment::create($request->all());
    return back()->with(
      'success',
      'Commentaire ajouté'
    );
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
    UserComment::find($id)->update($request->only('content'));
    return back()->with(
      'success',
      'Commentaire modifié'
    );
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    UserComment::destroy($id);
    return back()->with(
      'success',
      'Commentaire supprimé'
    );
  }
}
