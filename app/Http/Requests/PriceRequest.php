<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceRequest extends FormRequest
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
        if (request()->has('price')) {
            return [
                'price' => 'required|numeric|min:1',
                'reduced_price' => 'nullable|numeric|min:1',
            ];
        } else {
            return [
                'trimesters.*.id' => 'integer|exists:trimesters,id',
                'trimesters.*.price' => 'required_with:trimesters.*.id|numeric|min:1',
                'trimesters.*.reduced_price' => 'nullable|numeric|min:1',

                'passes.*.id' => 'integer|exists:passes,id',
                'passes.*.price' => 'required_with:passes.*.id|numeric|min:1',
                'passes.*.reduced_price' => 'nullable|numeric|min:1',
            ];
        }
    }

    public function messages()
    {
        $messages = [];
        if (!request()->has('price')) {

            foreach ($this->get('trimesters') as $key => $val) {
                $messages["trimesters.$key.price.required_with"] = "Le prix de Trimestre " . ($key + 1) . " est invalide";
                $messages["trimesters.$key.price.integer"] = "Le prix est invalide";
            }
            foreach ($this->get('passes') as $key => $val) {
                $messages["passes.$key.price.required_with"] = "Le prix du " . request()->passes[$key]['name'] . " est invalide";
                $messages["passes.$key.price.integer"] = "Le prix est invalide";
            }
        }
        return $messages;
    }
}
