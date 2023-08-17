<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
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
      'name' => 'required|min:3',
      'description' => 'required',
      'duration' => [
        'required',
        'date_format:H:i',
        function ($attribute, $value, $fail) {
          $time_ = explode(':', request()->duration);
          if ($time_[0]) {
            $hour = intval($time_[0]);
            $validated = ($hour <= 2 && $hour >= 0);

            if (!$validated) {
              $fail("La durée n'est pas valide.");
            }
          }
        }
      ],
      'activity_category_id' => 'nullable|exists:activity_categories,id',
    ];
  }
}
