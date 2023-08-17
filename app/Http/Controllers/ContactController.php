<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Contact;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Establishment;

class ContactController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    /** Page Liste contact */
    $contacts = Contact::search()->order()->with(['establishment', 'user'])->paginate(page_limit());

    foreach ($contacts as $key => $contact) {
      $contacts[$key]->content_min = Str::limit($contact->content, 20);
    }

    return Inertia::render('Contact/index', compact('contacts'));
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    /** Page formulaire contact */
    $establishments = Establishment::where('relaxation_center', 0)->get();
    return Inertia::render('Contact/create', compact('establishments'));
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    Contact::create($request->all());
    return redirect()->route('contact.create')->with('success', 'Message envoyé');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $contact = Contact::find($id);
    // return Inertia::render('Contact/show', compact('contact'));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $contact = Contact::find($id);
    $contact->delete();
    return redirect()->route('contact.index')->with('success', 'Message supprimé');
  }
}
