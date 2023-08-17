<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use App\Http\Resources\InvoiceResource;
use Inertia\Inertia;

class BillController extends Controller
{
  public function bill_detail(Request $request, $bill_id)
  {
    $QueryBillDetail = Bill::where('id', $bill_id)->with('subscriptions', 'subscriptions.pass', 'subscriptions.establishment', 'subscriptions.activities', 'fees', 'fees.season', 'user', 'payment');

    $BillDetail = $QueryBillDetail->first();
    $BillDetail = (new InvoiceResource($QueryBillDetail->first()));

    if ($request->has('dd')) {
      dd(json_decode(json_encode($BillDetail)));
    }
    return $BillDetail;
  }

  public function unpaidInvoice(Request $request)
  {
    $invoicesQuery = Bill::with(['payment', 'user', 'season'])
      ->doesntHave('payment');

    $invoices = $invoicesQuery->search()->filter()->paginate(page_limit());

    return Inertia::render('Invoice/Unpaid/index', compact('invoices'));
  }

  public function _list()
  {
    return Bill::with(['payment', 'user'])->whereHas('user')->orderByRaw("TIMEDIFF(predictable_payment_date, NOW()) ASC, predictable_payment_date ASC")->filter()->paginate(page_limit());
  }
}
