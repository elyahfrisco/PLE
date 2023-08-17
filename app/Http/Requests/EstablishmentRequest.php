<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstablishmentRequest extends FormRequest
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
            'address' => 'required|min:5',
            'email' => 'required|email',
            'postal_code' => 'required|integer|min:4',
            'phone' => 'required|min:10',
            'start_time' => 'required',
            'end_time' => 'required',
            'latitude' => 'required_with:longitude',
            'longitude' => 'required_with:latitude',
        ];
    }
}
