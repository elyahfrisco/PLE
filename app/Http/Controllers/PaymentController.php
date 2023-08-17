<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Inertia\Inertia;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaymentRequest;

class PaymentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $payments = $this->_list($request);
    return Inertia::render('Payment/Admin/index', compact('payments'));
  }

  public function _list(Request $request)
  {
    $paymentQuery = Bill::with(['payment' => function ($query) {
      $query->orderByDesc('date');
    }, 'user', 'season'])
      ->whereHas('payment');

    if (is_numeric($request->user_id)) {
      $paymentQuery->where('user_id', $request->user_id);
    }

    if (is_numeric($request->establishment_id)) {
      $paymentQuery->where('establishment_id', $request->establishment_id);
    }

    if (is_numeric($request->season_id)) {
      $paymentQuery->where('season_id', $request->season_id);
    }

    return $paymentQuery->search()->paginate(page_limit());
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
  public function store(PaymentRequest $request)
  {
    $data = $request->all();

    $data['admin_id'] = auth()->user()->id;
    $data['reference'] = 'PAY_FAC_' . $request->bill_id;

    $payment = Payment::create($data);

    /** update poayment_id in subscription table */
    $payment->subscriptions()->update(['payment_id' => $payment->id]);

    return back()->with('success', 'Facture réglée');
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

  public function payments_methods(Request $request)
  {
    return [
      ['name' => 'Espece'],
      ['name' => 'Par chèque'],
      ['name' => 'Virement bancaire'],
      ['name' => 'Carte bancaire'],
    ];
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {

    $payment = Payment::find($id);
    $payment->subscriptions()->update(['payment_id' => null]);
    $payment->delete();
    return back()->with('success', 'Paiement supprimé');
  }
}
