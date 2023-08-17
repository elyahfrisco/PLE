<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssistantController extends Controller
{
  /**
   * Show the form for creating a new customer.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $users = User::AssistantRole()
      ->search()
      ->order()
      ->paginate(page_limit());
    return Inertia::render('Account/Assistant/index', compact('users'));
  }

  /**
   * Show the form for creating a new customer.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $account_type = 'assistant';
    return Inertia::render('Account/Assistant/create', compact('account_type'));
  }
}
