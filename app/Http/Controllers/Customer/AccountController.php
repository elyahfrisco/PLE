<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Activity;
use App\Models\UserWish;
use App\Models\UserPhone;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\CustomerRequest;

class AccountController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $user = auth()->user();
    return Inertia::render('Customer/Account/index', compact('user'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {

    $customer = User::withTrashed()->with(['phones'])->find($id);
    return Inertia::render('Customer/Account/edit', compact('customer'));
  }

  /**
   * Show the form for editing the customer's profil photo.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit_photo($id)
  {
    $customer = User::withTrashed()->find($id);
    return Inertia::render('Customer/Account/editPhoto', compact('customer'));
  }

  /**
   * Update the customer's profil photo.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update_photo(Request $request, $id)
  {
    $request->validate([
      'profile_photo' => "required|max:2048"
    ]);

    $customer = User::withTrashed()->find($id);

    $fileName =  $customer->id . "_" . time() . '.' . $request->profile_photo->extension();

    $path = $request->profile_photo->move(public_path(config('app.path_profil_photo')), $fileName);

    $customer->profile_photo_path = $fileName;

    $customer->save();

    return redirect()->route('account.index');
  }


  /**
   * Show the form for editing the customer's medical certifiacte.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit_medical_certificate($id)
  {
    $customer = User::withTrashed()->find($id);
    return Inertia::render('Customer/Account/editMedicalCertificate', compact('customer'));
  }

  /**
   * Update the customer's medical certifiacte.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update_medical_certificate(Request $request, $id)
  {
    $request->validate([
      'medical_certificate' => "required|max:2048|mimes:pdf,doc,docx,png,jpg"
    ]);

    $customer = User::find($id);

    $fileName =  $customer->id . "_" . $customer->name . "_medical_certificate_" . time() . '.' . $request->medical_certificate->extension();

    $path = $request->medical_certificate->move(public_path(config('app.path_medical_certificate')), $fileName);

    $customer->medical_certificate_path = $fileName;

    $customer->save();

    return redirect()->route('account.index');
  }

  /**
   * Update the customer.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(CustomerRequest $request, $id)
  {

    if ($request->filled('password')) {
      $request->offsetSet('password', Hash::make($request->password));
    }

    $customer = User::find($id);
    $customer->update($request->all());

    /*Delete phones*/
    $id_updated = Arr::pluck($request->phones, 'id');
    $customer->phones()->whereNotIn('id', $id_updated)->delete();

    $data_phones = array_map(function ($phone) {
      return Arr::only($phone, ['id', 'phone', 'owner', 'type', 'user_id']);
    }, $request->phones);

    /* Update or insert phones */
    UserPhone::upsert(
      $data_phones,
      ['id'],
      ['phone', 'owner', 'type', 'user_id'],
    );

    session()->flash('success', "Profil modifié");
    return redirect()->route('account.index');
  }
}
