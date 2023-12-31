<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClosingRequest extends FormRequest
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
        $validation = [
            'date_start' => 'required|date',
            'reason' => 'required|min:5',
        ];

        if (!request()->oneDay) {
            $validation['date_end'] = 'required|after:date_start';
        }

        return $validation;
    }
}
