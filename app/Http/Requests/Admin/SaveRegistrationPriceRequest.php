<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveRegistrationPriceRequest extends FormRequest
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
    if ($this->category === 'adult') {
      return [
        'price_adult' => 'required|numeric',
        'reduced_price_adult' => 'nullable|numeric|lte:price_adult',
      ];
    } elseif ($this->category === 'child') {
      return [
        'price_child' => 'required|numeric',
        'reduced_price_child' => 'nullable|numeric|lte:price_child',
      ];
    }
  }
}
