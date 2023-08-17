<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeasonRequest extends FormRequest
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
    return [
      'date_start' => 'required|date',
      'date_end' => 'required|date|after:date_start',
      'year_start' => 'required|integer|min:' . (intval(date('Y')) - 1),
      'year_end' => 'required|integer|gt:year_start|min:' . (intval(date('Y')) - 1),
    ];
  }
}
