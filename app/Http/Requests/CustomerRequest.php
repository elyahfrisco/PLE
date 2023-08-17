<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    // dd(request()->all());
    $rules = [
      "name" => "required|string|min:2",
      "first_name" => "required|string|min:2",
      "birth_date" => "required|date",
      "gender" => "required|in:male,female,child",
      "address" => "required|string|min:2",
      // "postal_code" => "required|string|min:5",
      "city" => "required|string|min:3",
      "phones.*.phone" => "required|min:10",
      // "activities" => "required|array",
      // "additional_information" => "string|min:20",
    ];

    if (!request()->is_child) {
      $rules['email'] = "required|email|unique:users,email" . (request()->id ? "," . request()->id . ',id' : '');
    }

    if ((!is_numeric(request()->id) || !auth()->user())
      && !in_array(request()->account_type, [
        'admin',
        'assistant',
        'coach',
        'intervenant',
      ])
    ) {
      $rules['contact_origin'] = 'required|string|min:5';
      // $rules['additional_information'] = 'string';
    }

    if (!auth()->user() || request()->method() == "PUT") {
      $rules['password'] = 'required_with:password_confirm|string|same:password_confirm|min:6';
      $rules['password_confirm'] = 'required_with:password_confirm|same:password|min:6';
    }

    if (request()->method() == "POST") {
      $rules['phone'] = 'required|min:10';
      if (request()->phone2) {
        $rules['phone2'] = 'min:10';
      }
    }

    if (request()->is_child) {
      if (!empty_(request()->mail1)) {
        $rules['mail1'] = 'email';
      }
      if (!empty_(request()->mail2)) {
        $rules['mail2'] = 'email';
      }

      if (request()->contact_origin == "") {
        unset($rules['contact_origin']);
      }
      unset($rules['first_name']);
    }

    if (request()->role == 'prospect' || request()->account_type == 'prospect') {
      unset($rules['first_name']);
      unset($rules['birth_date']);
      unset($rules['address']);
      unset($rules['city']);

      if (request()->password == "" && request()->method() == "PUT") {
        unset($rules['password']);
      }

      if (request()->phone == "") {
        unset($rules['phone']);
      }

      if (request()->contact_origin == "") {
        unset($rules['contact_origin']);
      }

      if (request()->postal_code == "") {
        unset($rules['postal_code']);
      }
    }

    return $rules;
  }

  public function messages()
  {
    return [
      'phones.*.phone.min' => 'The phone must be at least 10 characters.',
      'planning_id' => 'Vouz devez choisir le creaneau horaire du client',
    ];
  }
}
