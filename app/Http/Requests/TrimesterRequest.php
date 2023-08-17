<?php

namespace App\Http\Requests;

use App\Models\Trimester;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TrimesterRequest extends FormRequest
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

    // $rule_exist = Rule::unique('trimesters')->where(function ($query) {
    //     return $query->where('season_id', request()->season_id)
    //         ->where('num_trimester', request()->num_trimester);
    // });

    // if (request()->id) {
    //     $rule_exist = $rule_exist->ignore(request()->id, 'id');
    // }

    return [
      'num_trimester' => [
        'required',
        'integer',
        'max:3',
        // $rule_exist
      ],
      'date_start' => [
        'required',
        'date',
        // function ($attribute, $value, $fail) {
        //     $last_trimester = Trimester::where('season_id', request()->season_id)
        //         ->where(function ($query) {
        //             $query->where('date_start', '<=', request()->date_start)->where('date_end', '>=', request()->date_start);
        //             if (request()->id) {
        //                 $query->where('id', '!=', request()->id);
        //             }
        //         })
        //         ->first();
        //     if ($last_trimester) {
        //         $fail('La date ajouté existe déjà');
        //     }
        // }
      ],
      'date_end' => [
        'required',
        'date',
        // function ($attribute, $value, $fail) {
        //     $last_trimester = Trimester::where('season_id', request()->season_id)
        //         ->where(function ($query) {
        //             $query->where('date_start', '<=', request()->date_end)->where('date_end', '>=', request()->date_end);
        //             if (request()->id) {
        //                 $query->where('id', '!=', request()->id);
        //             }
        //         })
        //         ->first();
        //     if ($last_trimester) {
        //         $fail('La date ajouté existe déjà');
        //     }
        // },
        'after:date_start',
      ],
      // 'week_count' => [
      //     'required',
      //     'integer',
      //     function ($attribute, $value, $fail) {
      //         $date_start = new \DateTime(request()->date_start);
      //         $date_end = new \DateTime(request()->date_end);
      //         $validated = ($value < ceil($date_start->diff($date_end)->days / 7));
      //         if (!$validated) {
      //             $fail('Le nombre de semaine est invalide');
      //         }
      //     },
      // ],
      'season_id' => 'required|integer|exists:seasons,id',
    ];
  }
}
