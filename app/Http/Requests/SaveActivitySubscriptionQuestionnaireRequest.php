<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveActivitySubscriptionQuestionnaireRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return auth()->check();
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'type' => 'required',
      'content' => 'required',
      'other_response' => 'required',
      'other_response_placeholder' => ($this->type === 'single' && !count($this->answers) ? 'required' : 'nullable'),
      'answers' => 'array' . ($this->type === 'multiple' ? '|min:1' : ''),
      'answers.*.content' => 'required',
    ];
  }

  public function attributes()
  {
    return [
      'content' => 'question',
      'answers' => 'reponses',
      'answers.*.content' => 'reponse',
      'other_response_placeholder' => "texte d'indice pour l'autre réponse",
    ];
  }
}
