<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user() ? true : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'establishment_id' => 'required',
            'season_id' => 'required',
            'user_id' => 'required',
            'subscriptionData' => function ($attribute, $value, $fail) {
                if (!is_array(request()->subscriptionData) || count(request()->subscriptionData) == 0) {
                    $fail("Vous devez choisir au moin un Pass");
                }
            },
            'predictable_payment_date' => 'required|after_or_equal:today',
            'payment_method' => 'required',
        ];
    }
}
