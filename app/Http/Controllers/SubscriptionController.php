<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
  protected $relatedTableToGet = ['establishment', 'season', 'pass', 'customer', 'activities',  'payment', 'renewal', 'bill'];

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $subscriptions = Subscription::extractDateSessions(
      Subscription::with($this->relatedTableToGet)
        ->where('user_id', auth()->user()->id)
        ->search()
        ->order()
        ->filter()
        ->paginate()->appends(request()->query())
    );
    return Inertia::render('Subscription/index', compact('subscriptions'));
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function facture()
  {
    $subscriptions = Subscription::extractDateSessions(
      Subscription::with($this->relatedTableToGet)
        ->where('user_id', auth()->user()->id)
        ->search()
        ->order()
        ->filter()
        ->paginate()->appends(request()->query())
    );
    return Inertia::render('Subscription/facture', compact('subscriptions'));
  }
}
