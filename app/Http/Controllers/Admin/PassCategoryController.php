<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pass;
use Inertia\Inertia;
use App\Models\PassCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PassCategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $pass_categories = PassCategory::all();
    return Inertia::render("Admin/Pass/Category/index", compact('pass_categories'));
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function passes($pass_category_id)
  {
    $pass_categories = Pass::wherehas('pass_categories', function ($query) use ($pass_category_id) {
      $query->where('id', $pass_category_id);
    });
    return Inertia::render("Admin/Pass/Category/index", compact('pass_categories'));
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
  public function store(Request $request)
  {
    $request->validate(['name' => 'required|string|min:3']);
    PassCategory::create($request->all());
    return redirect()->route('passes.categories.index')->with('success', 'Categorie Pass ajouté');
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
    $pass = PassCategory::find($id);
    $pass->update($request->all());
    return redirect()->route('passes.categories.index')->with('success', 'Categorie Pass modifié');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    PassCategory::destroy($id);
    return back()->with('success', 'Categorie Pass supprimé');
  }
}
