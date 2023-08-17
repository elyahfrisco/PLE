<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Queue;
use Illuminate\Http\Request;
use App\Models\RecuperationRequest;

class QueueController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    if (auth()->user()->isCustomer()) {
      return Inertia::render('Customer/Queue/index');
    } else {
      return Inertia::render('Admin/Queue/index');
    }
  }

  /**
   * List For API.
   *
   * @return \Illuminate\Http\Response
   */
  public function _list(Request $request)
  {
    $queueQuery = Queue::select('queues.*');
    $queueQuery->withRelations()->filter()->search()->order();
    return $queueQuery->paginate(page_limit());
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function _detail($queue_id)
  {
    $queueQuery = Queue::select('queues.*')
      ->withRelations()
      ->where('queues.id', $queue_id);
    return response($queueQuery->first());
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
