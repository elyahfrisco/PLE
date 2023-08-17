<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
  public function show($id)
  {
    return Inertia::render('Components/InfoInvoice', ['bill_id' => $id, 'print' => request()->print, 'hide_btn_print' => false]);
  }
}
