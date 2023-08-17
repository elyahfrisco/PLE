<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionComment;
use Illuminate\Http\Request;

class SubscriptionCommentController extends Controller
{

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, Subscription $subscription)
  {
    $subscription->comments()->create($request->only('content'));
    return back()->with('success', 'Commentaire ajouté');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, SubscriptionComment $comment)
  {
    $comment->update($request->only('content'));
    return back()->with('success', 'Commentaire modifié');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(SubscriptionComment $comment)
  {
    $comment->delete();
    return back()->with('error', 'Commentaire supprimé');
  }
}
