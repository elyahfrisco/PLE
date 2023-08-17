<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PlanningRequest extends FormRequest
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

    $validations = [
      'day' => 'required|filled',
      'time_start' => 'required|date_format:H:i',
      'time_end' => 'required|date_format:H:i|different:time_start|after:time_start',
      'max_effective' => 'required|integer|min:1',
      // 'trimester_num' => 'integer|min:1|max:3',
      // 'number_activity_sessions' => 'required|integer|min:1',
      'activity_id' => 'required',
      'season_id' => 'required|integer',
    ];

    $validations['start_at'] = [
      'required',
      'date',
      // function ($attribute, $value, $fail) {
      //     $day_name_start = strtolower(Carbon::parse(request()->start_at)->englishDayOfWeek);
      //     if (request()->day != $day_name_start) {
      //         $fail("Le jour de la date sélectionnée doit correspondre au jour sélectionné");
      //     }
      // }
    ];

    $validations['end_at'] = [
      'required',
      'date',
      'after_or_equal:start_at',
      // function ($attribute, $value, $fail) {
      //     $day_name_end = strtolower(Carbon::parse(request()->end_at)->englishDayOfWeek);
      //     if (request()->day != $day_name_end) {
      //         $fail("Le jour de la date sélectionnée doit correspondre au jour sélectionné");
      //     }
      // }
    ];

    if (request()->trimester_num) {
      // $validations['trimester_num'] = 'required|integer|min:1|max:3';
    }

    return $validations;
  }
}
