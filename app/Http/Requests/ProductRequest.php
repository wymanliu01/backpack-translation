<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

//testing
class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sku' => 'required',
            'price' => 'required',
            'translations' => function ($attribute, $value, $fail) {
                $inputs = json_decode($value);
                foreach ($inputs as $input) {
                    if (empty($input->name)) {
                        return $fail(sprintf('One of the elements in the %s was not entered in the "Name" field', $attribute));
                    }
                }

            },
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
