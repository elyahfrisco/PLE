<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
  /**
   * Show the form for creating a new customer.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $users = User::AdminRole()
      ->search()
      ->order()
      ->paginate(page_limit());
    return Inertia::render('Account/Admin/index', compact('users'));
  }

  /**
   * Show the form for creating a new customer.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $account_type = 'admin';
    return Inertia::render('Account/Admin/create', compact('account_type'));
  }
}
