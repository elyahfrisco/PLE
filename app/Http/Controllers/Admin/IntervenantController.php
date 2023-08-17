<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IntervenantController extends Controller
{
  /**
   * Show the form for creating a new customer.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $users = User::IntervenantRole()->orderBy('first_name')->paginate(page_limit());
    return Inertia::render('Account/Coach/index', compact('users'));
  }

  /**
   * Show the form for creating a new customer.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $account_type = 'intervenant';
    return Inertia::render('Account/Coach/create', compact('account_type'));
  }
}
